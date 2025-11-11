<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChangePassword extends Component
{
    public $userId;
    public $showPassword = false;

    #[validate(as: 'Mật khẩu mới')]
    public string $newPassword='';

    #[validate(as: 'Xác nhận mật khẩu mới')]
    public string $confirmNewPassword='';

    public function render()
    {
        return view('livewire.admin.users.change-password');
    }

    public function updatePassword()
    {
        $this->validate();
        $user = User::find($this->userId);
        if($user){
            $user->password = bcrypt($this->newPassword);
            $user->email_verified_at = now();
            $user->save();
            $this->dispatch('alert',type:'success',message:'Đổi mật khẩu thành công!');
            $this->reset(['newPassword','confirmNewPassword']);
        }
        else{
            $this->dispatch('alert',type:'error',message:'Người dùng không tồn tại!');
        }

    }

    public function rules(){
        return [
            'newPassword' => 'required|string|min:8|different:currentPassword',
            'confirmNewPassword' => 'required|string|same:newPassword',
        ];
    }
}
