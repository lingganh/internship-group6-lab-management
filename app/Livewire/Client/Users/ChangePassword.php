<?php

namespace App\Livewire\Client\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChangePassword extends Component
{
    protected $listeners = [
        'confirmChangePassword' => 'changePassword'
    ];
    #[validate(as: 'Mật khẩu hiện tại')]
    public string $currentPassword='';

    #[validate(as: 'Mật khẩu mới')]
    public string $newPassword='';

    #[validate(as: 'Xác nhận mật khẩu mới')]
    public string $confirmNewPassword='';

    public $showPassword = false;
    public function render()
    {
        return view('livewire.client.users.change-password');
    }

    public function rules(){
        return [
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|different:currentPassword',
            'confirmNewPassword' => 'required|string|same:newPassword',
        ];
    }

    public function openModel()
    {
        $this->validate();
        $this->dispatch('openModel',type:'warning',title:'Bạn có chắc chắn muốn thay đổi mật khẩu không?',confirmEvent:'confirmChangePassword');
    }

    public function changePassword(){
        $user = auth()->user();
        if($user){
            if (!password_verify($this->currentPassword, $user->password)) {
                $this->dispatch('alert',type:'error',message:'Mật khẩu hiện tại không đúng!');
                $this->currentPassword='';
                return 0;
            }
            else{
                $user->password = bcrypt($this->newPassword);
                $user->save();
                Auth::logout();
                session()->flash('success','Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
                return redirect()->route('login')  ;
            }
        }
        else{
            $this->dispatch('alert',type:'error',message:'Người dùng không tồn tại!');
            return 0;
        }
    }
}
