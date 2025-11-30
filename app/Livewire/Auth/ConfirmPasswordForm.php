<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Validate;

class ConfirmPasswordForm extends Component
{
    public $showPassword = false;

    #[validate(as: 'Mật khẩu')]
    public $password = '';

    public function render()
    {
        return view('livewire.auth.confirm-password-form');
    }

    public function confirmPassword(){
        $this->validate();

        $confirmed = auth()->validate([
            'email' => auth()->user()->email,
            'password' => $this->password,
        ]);
        if ($confirmed) {
            session(['auth.password_confirmed_at' => time()]);
            session()->flash('success', 'Xác nhận mật khẩu thành công.');
            return redirect()->intended(route('home'));
        } else {
            $this->dispatch('alert', type: 'error', message: 'Mật khẩu không đúng. Vui lòng thử lại.');
            $this->password = '';
        }
    }

    public function showPasswords(){
        $this->showPassword = !$this->showPassword;
    }

    protected function rules()
    {
        return [
            'password' => 'required|string|min:8',
        ];
    }
}
