<?php

namespace App\Livewire\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormLogin extends Component
{
    #[validate(as: 'Mã GV/Email')]
    public string $username='';

    #[validate(as: 'Mật khẩu')]
    public string $password='';

    public $remember=false;

    public function render()
    {
        return view('livewire.auth.form-login');
    }

    public function rules(){
        return [
            'username' => [
                'required',
                'string',
            ],
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
                $this->dispatch('alert',type:'error',message: 'Tài khoản chưa được thiết lập mật khẩu. Vui lòng chọn quên mật khẩu để thiết lập.');
                return 0;
            }
        }
        else{
            $this->dispatch('alert',type:'error',message: 'Tài khoản không tồn tại.');
            return 0;
        }
        if(!password_verify($this->password, $user->password)){
            $this->dispatch('alert',type:'error',message: 'Mật khẩu không đúng. Vui lòng thử lại.');
            $this->password='';
            return 0;
        }

        if($user->status === UserStatus::Pending->value){
            $this->dispatch('alert',type:'warning',message: 'Tài khoản của bạn đang chờ phê duyệt. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.');
            $this->reset('password', 'username');
            return 0;
        }

        if($user->status === UserStatus::Archived->value){
            $this->dispatch('alert',type:'error',message: 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.');
            $this->reset('password', 'username');
            return 0;
        }

        if ($user->two_factor_confirmed_at) {
            session([
                'login.id' => $user->id,
                'login.remember' => $this->remember ?? false // Nếu bạn có checkbox "Ghi nhớ", thay false bằng biến đó
            ]);

            // Chuyển hướng người dùng sang trang nhập mã OTP
            return redirect()->to('/two-factor-challenge');
        }


        //dang nhap thanh cong
        Auth::login($user, $this->remember ?? false);
        $user->last_login_at = now();
        $user->save();
        session()->flash('success', 'Đăng nhập thành công!');
        return redirect()->route('home');
    }

}
