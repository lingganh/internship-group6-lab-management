<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SettingPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use App\Enums\Role;
use App\Models\Role as ModelsRole;

class AuthenticateController extends Controller
{
    public function redirectToSSO(){
        $query = http_build_query([
            'client_id' => config('auth.sso.client_id'),
            'redirect_uri' => route('sso.callback'),
            'response_type' => 'code',
            'scope' => '',
        ]);
        return redirect(config('auth.sso.uri').'/oauth/authorize?'.$query);
    }

    public function handleSSOCallback(Request $request){
        try {
            $data = $this->getAccessToken($request->code);
            if(!isset($data['access_token'])){
                return abort(401);
            }
            $userData = $this->getUserData($data['access_token']);
            $user = $this->findOrCreateUser($userData, $data['access_token']);
            Auth::login($user);
            session()->flash('success', 'Đăng nhập thành công!');
            return redirect()->route('home');

        }
        catch (\Exception $exception){
            dd($exception->getMessage());
        }
    }

    public function getAccessToken(string $code)
    {
        $response = Http::asForm()->post(config('auth.sso.uri').'/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('auth.sso.client_id'),
            'client_secret' => config('auth.sso.client_secret'),
            'redirect_uri' => route('sso.callback'),
            'code' => $code,
        ]);

        return $response->json();
    }

    public function getUserData(string $accessToken)
    {
        $response = Http::withToken($accessToken)->get(config('auth.sso.uri').'/api/user');
        return $response->json();
    }

    public function findOrCreateUser(array $userData, string $accessToken){
        $userData = array_merge($userData, ['access_token' => $accessToken]);
//        dd($userData);

        $user = User::where('sso_id',$userData['id'])->first();
        if($user){
            User::where('sso_id', $userData['id'])->update([
                'access_token' => $userData['access_token'],
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'last_login_at'=> now(),
                'code' => $userData['code'] ?? null,
            ]);
        }
        else{
            $user = User::where('code',$userData['code'])->first();
            if($user){
                //cap nhat sso_id neu da co tai khoan truoc do
                $user->update([
                    'sso_id' => $userData['id'],
                    'access_token' => $userData['access_token'],
                    'full_name' => $userData['full_name'],
                    'email' => $userData['email'],
                    'last_login_at'=> now(),
                ]);
                return User::where('sso_id',$userData['id'])->first();
            }
            //tao moi tai khoan
            else{
                $userType = $this->determineUserType($userData['role']);
//                if($userData['code']==='CN01' or $userData['code']==='CNP02'){
//                    $userType = Role::Admin->value;
//                }
                $roleId = ModelsRole::where('name', $userType)->first()->id;
                $user = User::create([
                    'full_name' => $userData['full_name'],
                    'email' => $userData['email'],
                    'sso_id' => $userData['id'],
                    'code' => $userData['code'] ?? null,
                    'last_login_at' => now(),
                    'access_token' => $userData['access_token'],
                    'role_id' => $roleId,
                ]);
                $token = Str::random(64);
                DB::table('password_reset_tokens')->updateOrInsert(
                    [   'email' => $user->email,],
                    [
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);

                Mail::to($user->email)->queue(new SettingPassword($user, $token));
            }
        }
        return $user;
    }

    private function determineUserType(string $role): string
    {
        return match ($role) {
            'superAdmin' => Role::Admin->value,
            'officer' => Role::Officer->value,
            default => Role::Student->value,
        };
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        session()->flash('warning', 'Đăng xuất thành công!');

        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        if(auth()->check()){
            return redirect()->route('home');
        }
        return view('pages.auth.login');
    }

    public function showRegisterForm()
    {
        if(auth()->check()){
            return redirect()->route('home');
        }
        return view('pages.auth.register');

    }

    public function setPassword($token)
    {
        $verifyToken = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();
        if (!$verifyToken) {
            return redirect()->route('home')->with('error', 'Liên kết không hợp lệ.');
        }
        elseif (Carbon::parse($verifyToken->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('home')->with('error', 'Liên kết đã hết hạn.');
        }
        $hasUser = User::where('email', $verifyToken->email)->first();
        if(!$hasUser){
            return redirect()->route('home')->with('error', 'Người dùng không tồn tại.');
        }

        session()->flash('success', 'Xác minh thành công! Vui lòng thiết lập mật khẩu.');
        return view('pages.auth.set-password', [
            'token' => $token,
            'email' => $verifyToken->email,
        ]);
    }

    public function forgotPassword()
    {
        return view('pages.auth.forgot-password');
    }

}
