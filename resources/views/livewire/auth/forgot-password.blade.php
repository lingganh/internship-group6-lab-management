<div>
    <form wire:submit.prevent="SubmitRequest" class="login-form">
        @csrf
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                <img src="{{asset('assets/images/FITA.png')}}" class="h-64px" alt="">
                <img src="{{asset('assets/images/logoST.jpg')}}" class="h-64px" alt="">
            </div>
            <h5 class="mb-0">Quên mật khẩu</h5>
            <span class="d-block text-muted">Nhập email SV/GV đã xác thực tài khoản của bạn</span>
            <div class="forget-pass-arrow"><a href="{{route('login')}}"><i class="ph-arrow-bend-up-left"></i></a></div>
        </div>

        @error('message')
        <label id="message-error" class="validation-error-label text-danger  w-100 text-center" for="basic">{{ $message }}</label>
        @enderror

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="email" type="text" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email " name="email" id="email"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-at text-muted"></i>
                </div>
                @error('email')
                <label id="error-email" class="validation-error-label text-danger" for="email">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100" >
                <span wire:loading wire:target="SubmitRequest" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                Gửi yêu cầu
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
