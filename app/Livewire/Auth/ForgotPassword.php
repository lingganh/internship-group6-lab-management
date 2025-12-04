<?php

namespace App\Livewire\Auth;

use App\Mail\ForgotPassword as MailForgotPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[validate(as: 'email')]
    public $email;

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    public function rules(){
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function SubmitRequest(){
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if($user){
            $token =  Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                [   'email' => $this->email,],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);

            Mail::to($this->email)->queue(new MailForgotPassword($user, $token));
            session()->flash('success', 'Yêu cầu đặt lại mật khẩu đã được gửi đến email của bạn.');
            return redirect(route('login'));
        }
        else
        {
            session()->flash('error', 'Email không tồn tại trong hệ thống.');
        }
        return 1;
    }
}
