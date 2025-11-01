<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
            $userType = $this->determineUserType($userData['role']);
            if($userData['code']==='CN01' or $userData['code']==='CNP02'){
                $userType = Role::Admin->value;
            }
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
        session()->flash('warning', 'Đăng xuất thành công!');

        return redirect()->route('home');
    }
}
