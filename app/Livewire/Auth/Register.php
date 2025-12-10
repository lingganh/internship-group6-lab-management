<?php

namespace App\Livewire\Auth;

use App\Mail\SettingPassword;
use App\Mail\VerifyUserEmail;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[validate(as: 'Họ và tên')]
    public string $fullName='';

    #[validate(as: 'Mã GV')]
    public string $code='';

    #[validate(as: 'Email')]
    public string $email='';


    public function render()
    {
        return view('livewire.auth.register');
    }

    public function Register(){
        $this->validate();
        $user = new User();
        $user->full_name = $this->fullName;
        $user->code = $this->code;
//        if(ctype_digit($this->code)){
//            // student
//            $email = $this->code."@sv.vnua.edu.vn";
//            $user->email=$email;
//            $roleId = Role::where('name', 'student')->first()->id;
//            $user->role_id = $roleId;
//            //gui mail xac nhan dang ky cho sinh vien
//            $user->save();
//            $token = Str::random(64);
//            DB::table('password_reset_tokens')->updateOrInsert(
//                [   'email' => $email,],
//                [
//                    'token' => $token,
//                    'created_at' => Carbon::now()
//                ]);
//
//            Mail::to($user->email)->queue(new VerifyUserEmail($user, $token));
//        }
//        else{
            // teacher
            $user->email=$this->email ."@vnua.edu.vn";
            $roleId = Role::where('name', 'teacher')->first()->id;
            $user->role_id = $roleId;
            //gui mail xac nhan dang ky cho sinh vien
            $user->save();
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                [   'email' => $user->email,],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);

            Mail::to($user->email)->queue(new VerifyUserEmail($user, $token));
//        }
        session()->flash('success', 'Đăng ký tài khoản thành công! Vui lòng kiểm tra email SV/GV để xác nhận tài khoản.');
        return redirect()->route('login');
    }
//    public function updatedEmail($value)
//    {
//        // Nếu người dùng nhập có chứa @ (ví dụ: fita1010@)
//        if (str_contains($value, '@')) {
//            // Chỉ lấy phần trước dấu @
//            $parts = explode('@', $value);
//            $this->email = $parts[0];
//
//            // Có thể thông báo nhẹ hoặc để user tự hiểu khi thấy text bị thay đổi
//        }
//    }

    public function rules(){
        return [
            'fullName' => 'required|string|max:255',
            'code' => 'required|string|max:8|unique:users,code',
            'email' => [
                'required',
                'not_regex:/@/', // Chặn ký tự @
                function (string $attribute, mixed $value, Closure $fail) {
                    // Tạo email đầy đủ giả định để check DB
                    $fullEmail = $value . '@vnua.edu.vn';

                    if (User::where('email', $fullEmail)->exists()) {
                        $fail("Email {$fullEmail} đã tồn tại trong hệ thống.");
                    }
                },
            ],
        ];
    }
    protected $messages = [
        'email.not_regex' => 'Vui lòng chỉ nhập tên email, không nhập ký tự @ hay đuôi email.',
    ];
}
