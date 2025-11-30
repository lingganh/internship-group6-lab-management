<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TwoFactorChallenge extends Component
{
    #[validate(as: 'Mã OTP')]
    public $otp='';
    #[validate(as: 'Mã khôi phục')]
    public $code='';

    public $isOtp=true;

    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }

    public function ConfirmOTP()
    {
        if($this->isOtp){
            $this->validateOnly('otp');
        }
        else{
            $this->validateOnly('code');
        }

        // 1. Lấy ID user đang chờ từ Session
        $userId = session('login.id');

        if (! $userId) {
            // Nếu không có session (ví dụ để lâu quá hết hạn), đá về trang login
            session()->flash('warning', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $isValid=false;
        if (!$user->two_factor_secret) {
            session()->flash('warning', 'Tài khoản chưa được thiết lập xác thực hai yếu tố.');
            return redirect()->route('login');
        }

        if($this->isOtp){
            // 2. Xác thực mã OTP
            // Lấy service xác thực của Fortify
            $provider = app(TwoFactorAuthenticationProvider::class);
            $isValid = $provider->verify(decrypt($user->two_factor_secret), $this->otp);
        }
        else{
            foreach ($user->recoveryCodes() as $code) {
                if ($code == $this->otp) {
                    $isValid = true;
                    // Nếu dùng mã khôi phục thì phải xóa mã đó đi (dùng 1 lần)
                    $user->replaceRecoveryCode($code);
                    break;
                }
            }
            if (! $isValid) {
                $this->addError('code', 'Mã khôi phục không chính xác.');
                return 0;
            }
        }

        if (! $isValid) {
            $this->addError('otp', 'Mã xác thực không chính xác.');
            return 0;
        }

        // 3. Đăng nhập thành công
        // Lấy trạng thái Remember Me từ session (nếu có)
        $remember = session('login.remember', false);

        Auth::login($user, $remember);

        // 4. Dọn dẹp Session
        session()->forget(['login.id', 'login.remember']);
        session()->regenerate();

        // 5. Chuyển hướng về trang chủ
        return redirect()->route('home');
    }

    protected function rules()
    {
        return [
            'otp' => 'required|string|size:6',
            'code' => 'required|string',
        ];
    }

    public function toggleOtpOrRecode(){
        $this->otp='';
        $this->resetErrorBag('otp');
        $this->isOtp=!$this->isOtp;
    }
}
