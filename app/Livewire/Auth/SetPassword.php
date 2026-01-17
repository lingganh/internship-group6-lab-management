<?php

namespace App\Livewire\Auth;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Enums\Role;
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

        $admins = User::whereHas('role', function ($q) {
            $q->where('name', Role::Admin->value);
        })->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id, // RECEIVER: user_id của người nhận thông báo

                // UI dùng để hiển thị: title + message (mô tả)
                'title'   => 'Đã tạo yêu cầu phê duyệt tài khoản mới!',
                'message' => 'Có một tài khoản mới đang chờ phê duyệt.',

                // data: payload để UI biết ai gửi + click đi đâu + liên kết tới đối tượng nghiệp vụ
                'data' => [
                    'request_id'  => $user->id,
                    'sender_id'   => $user->id,    // SENDER id
                    'sender_name' => $user->full_name ?? $user->name ?? 'Người dùng', // SENDER display
                    'url'         => route('admin.users.edit', $user->id), // Click chuyển trang
                ],
            ]);
        }


        session()->flash('success', 'Thiết lập mật khẩu thành công! Vui lòng đăng nhập.');
        return redirect()->route('login');
    }
}
