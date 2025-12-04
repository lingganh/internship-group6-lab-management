<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SetPassword extends Component
{
    public $email;
    public $token;
    public $showPassword=false;

    #[validate(as: 'Mật khẩu mới')]
    public string $password = '';

    #[validate(as: 'Nhập lại mật khẩu')]
    public string $confirmPassword = '';


    public function render()
    {
        return view('livewire.auth.set-password');
    }

    public function rules(){
        return [
            'password' => 'required|min:8|max:32',
            'confirmPassword' => 'required|same:password',
        ];
    }

    public function SetPasswordUser(){
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if($user) {
            $user->password = bcrypt($this->password);
            $user->email_verified_at = now();
            $user->save();
        }

        //xoa token sau khi dat lai mk
        DB::table('password_reset_tokens')->where('token', $this->token)->delete();
        Auth::logout();
        session()->flash('success', 'Thiết lập mật khẩu thành công! Vui lòng đăng nhập.');
        return redirect()->route('login');
    }
}
