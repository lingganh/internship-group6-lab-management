<div>
    <form wire:submit.prevent="ConfirmOTP" class="login-form">
        @csrf
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                <img src="{{asset('assets/images/FITA.png')}}" class="h-64px" alt="">
                <img src="{{asset('assets/images/logoST.jpg')}}" class="h-64px" alt="">
            </div>
            <h5 class="mb-0">Nhập mã xác thực được ứng dụng Authenticator của bạn cung cấp.</h5>
        </div>

        @error('message')
        <label id="message-error" class="validation-error-label text-danger w-100 text-center" for="basic">{{ $message }}</label>
        @enderror
        <div class="mb-3">
            @if($isOtp)
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="otp" type="number" class="form-control fs-14 @error('otp') is-invalid @enderror" placeholder="######" oninput="this.value = this.value.slice(0, 6)"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-key text-muted"></i>
                </div>
                @error('otp')
                <label id="error-otp" class="validation-error-label text-danger" for="otp">{{ $message }}</label>
                @enderror
            </div>
            @else
                <div class="form-control-feedback form-control-feedback-start">
                    <input wire:model.live="code" type="text" class="form-control fs-14 @error('code') is-invalid @enderror" placeholder="Nhập mã khôi phục"/>
                    <div class="form-control-feedback-icon">
                        <i class="ph-key text-muted"></i>
                    </div>
                    @error('code')
                    <label id="error-code" class="validation-error-label text-danger" for="code">{{ $message }}</label>
                    @enderror
                </div>
            @endif
        </div>

        <div class="my-3">
            <button type="submit" class="btn btn-primary w-100">
                <span wire:loading wire:target="ConfirmOTP" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                Xác nhận
            </button>
        </div>

        <div class="text-center  mb-3">
            @if($isOtp)
            <div href="{{route('forgotPassword')}}" class="ms-auto">hoặc bạn có thể <u class="fw-bold cursor-pointer" @click="$wire.toggleOtpOrRecode">đăng nhập bằng mã khôi phục</u></div>
            @else
            <div href="{{route('forgotPassword')}}" class="ms-auto">hoặc bạn có thể <u class="fw-bold cursor-pointer" @click="$wire.toggleOtpOrRecode">đăng nhập bằng mã OTP</u></div>
            @endif
        </div>
    </form>
</div>
