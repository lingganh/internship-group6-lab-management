<div>
    <form wire:submit.prevent="Register" class="login-form">
        @csrf
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                <img src="{{asset('assets/images/FITA.png')}}" class="h-64px" alt="">
                <img src="{{asset('assets/images/logoST.jpg')}}" class="h-64px" alt="">
            </div>
            <h5 class="mb-0">Đăng ký tài khoản</h5>
        </div>
        <div class="mb-3">
            <label class="form-label">Họ và tên: </label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="fullName" type="fullName" class="form-control @error('fullName') is-invalid @enderror" placeholder="Nhập họ và tên" id="fullName" name="fullName"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-user-circle text-muted"></i>
                </div>
                @error('fullName')
                <label id="error-fullName" class="validation-error-label text-danger" for="fullName">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Mã SV-GV:</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="code" type="text" class="form-control @error('code') is-invalid @enderror" placeholder="Nhập Mã SV/GV" name="code" id="code"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-lock-simple text-muted"></i>
                </div>
                @error('code')
                <label id="error-code" class="validation-error-label text-danger" for="code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email SV/GV" id="email" name="email"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-at text-muted"></i>
                </div>
                @error('email')
                <label id="error-email" class="validation-error-label text-danger" for="email">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">
                <span wire:loading wire:target="Register" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                Đăng ký</button>
        </div>
        <div class="text-center text-muted content-divider mb-3">
            <span class="px-2">Bạn đã có tài khoản?</span>
        </div>
        <div class="mb-3">
            <a href="{{route('login')}}" class="btn btn-light w-100">Đăng Nhập</a>
        </div>
    </form>
</div>
