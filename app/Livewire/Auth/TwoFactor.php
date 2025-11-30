<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Component;

class TwoFactor extends Component
{
    protected $listeners = [
        'disableTwoFactor' => 'disableTwoFactor',
    ];
    public $code = '';
    // Quản lý trạng thái hiển thị
    public bool $showingQrCode = false;
    public bool $showingOtpInput = false;
    public bool $showModalOTP = false;

    public bool $showRecoveryCodes = false;

     // Quy tắc xác thực

    public function render()
    {
        return view('livewire.auth.two-factor');
    }
    // Lấy user hiện tại
    public function getUserProperty()
    {
        return Auth::user();
    }

    public function enableTwoFactor(EnableTwoFactorAuthentication $enable)
    {
        // Hành động này tạo secret key trong DB nhưng chưa confirmed
        $enable($this->user);

        // Hiển thị Modal QR Code
        $this->showingQrCode = true;
        $this->showingOtpInput = false;
    }

    public function openOtpModal()
    {
        $this->showingQrCode = false;
        $this->showingOtpInput = true;
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirm)
    {
        $this->validate([
            'code' => 'required|string|size:6',
        ]);

        try {
            $confirm($this->user, $this->code);

            // Thành công: Tắt hết modal, reset code
            $this->showingQrCode = false;
            $this->showingOtpInput = false;
            $this->code = '';
            // Có thể thêm flash message nếu muốn
            $this->dispatch('alert',type:' success ',message: 'Đã kích hoạt xác thực 2 yếu tố thành công!');

        } catch (\Exception $e) {
            $this->addError('code', 'Mã xác thực không hợp lệ. Vui lòng thử lại.');
        }
    }

    public function openModalConfirmDisable()
    {
        $this->dispatch('openModel', title:"Bạn có chắc chắn muốn tắt xác thực hai yếu tố không?", type:'warning', confirmEvent:'disableTwoFactor');

    }

    public function disableTwoFactor(DisableTwoFactorAuthentication $disable)
    {
        $disable($this->user);

        // Reset trạng thái
        $this->showingQrCode = false;
        $this->showingOtpInput = false;
        $this->code = '';
        $this->dispatch('alert',type:' success ',message: 'Đã tắt xác thực 2 yếu tố thành công!');
    }

    public function cancelShowQr()
    {
        $this->showingQrCode = false;
        $this->showingOtpInput = false;
        $this->code = '';
        $this->resetValidation('code');
    }

    public function cancelShowOtp()
    {
        $this->showingQrCode = true;
        $this->showingOtpInput = false;
        $this->code = '';
        $this->resetValidation('code');
    }

    public function ShowRecoveryCodes()
    {
        $this->showRecoveryCodes = !$this->showRecoveryCodes;
    }

    public function RegenerateCodes(GenerateNewRecoveryCodes $generate)
    {
        $generate($this->user);
        $this->dispatch('alert',type:' success ',message: 'Đã tạo lại mã khôi phục mới!');
    }

}
