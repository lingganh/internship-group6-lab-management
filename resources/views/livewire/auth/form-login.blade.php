<div>
    <form wire:submit.prevent="SubmitLogin" class="login-form">
        @csrf
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                <img src="{{asset('assets/images/FITA.png')}}" class="h-64px" alt="">
                <img src="{{asset('assets/images/logoST.jpg')}}" class="h-64px" alt="">
            </div>
            <span class="d-block text-muted">Chào mừng bạn đến với</span>
            <h5 class="mb-0">Hệ thống quản lý phòng lab</h5>
        </div>

        @error('message')
        <label id="message-error" class="validation-error-label text-danger  w-100 text-center" for="basic">{{ $message }}</label>
        @enderror

        <div class="mb-3">
            <label class="form-label">Mã SV-GV/Email</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="username" type="text" class="form-control @error('username') is-invalid @enderror" placeholder=" Mã SV/GV hoặc Email " name="username" id="username" value="{{ old('username') }}"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-user-circle text-muted"></i>
                </div>
                @error('username')
                <label id="error-username" class="validation-error-label text-danger" for="username">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="•••••••••••" id="password" name="password" value="{{ old('password') }}"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-lock text-muted"></i>
                </div>
                @error('password')
                <label id="error-password" class="validation-error-label text-danger" for="password">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="d-flex align-items-center mb-3">
            <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" value="1" wire:model="remember">
                <span class="form-check-label">Nhớ mật khẩu</span>
            </label>
            <a href="{{route('forgotPassword')}}" class="ms-auto">Quên mật khẩu</a>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">
                <span wire:loading wire:target="SubmitLogin" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                Đăng nhập
            </button>
        </div>
        <div class="text-center text-muted content-divider mb-3">
            <span class="px-2">Hoặc đăng nhập với</span>
        </div>
        <div class="text-center mb-3">
            <a href="{{route('sso.redirect')}}" class="btn btn-outline-primary btn-icon border-width-2 w-100">
                Hệ thống ST Single Sign-On
            </a>
        </div>
        <div class="text-center text-muted content-divider mb-3">
            <span class="px-2">Bạn chưa có tài khoản?</span>
        </div>
        <div class="mb-3">
            <a href="{{route('register')}}" class="btn btn-light w-100">Đăng ký</a>
        </div>
    </form>
</div>
