<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormLogin extends Component
{
    #[validate(as: 'Mã SV-GV/Email')]
    public string $username='';

    #[validate(as: 'Mật khẩu')]
    public string $password='';

    public function render()
    {
        return view('livewire.auth.form-login');
    }

    public function rules(){
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function SubmitLogin(){
        $this->validate();
        //xem nguoi dung nhap gi
        $user=null;
        if(str_contains($this->username, '@')){
            $user= User::where('email', $this->username)->first();
        }
        else{
            $user = User::where('code', $this->username)->first();
        }
        if($user){
            if($user->password == null)
            {
                $this->dispatch('alert',type:'error',message: 'Tài khoản chưa được thiết lập mật khẩu. Vui lòng thiết lập mật khẩu trước khi đăng nhập.');
                return;
            }
        }
        else{
            $this->dispatch('alert',type:'error',message: 'Tài khoản không tồn tại.');
            return;
        }
        if(!password_verify($this->password, $user->password)){
            $this->dispatch('alert',type:'error',message: 'Mật khẩu không đúng. Vui lòng thử lại.');
            $this->password='';
            return;
        }
        //dang nhap thanh cong
        Auth::login($user);
        $user->last_login_at = now();
        $user->save();
        session()->flash('success', 'Đăng nhập thành công!');
        return redirect()->route('home');
    }

}
