<div>
    <form wire:submit.prevent="SetPasswordUser" class="login-form">
        @csrf
        <div class="text-center mb-3">
            <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                <img src="{{asset('assets/images/FITA.png')}}" class="h-64px" alt="">
                <img src="{{asset('assets/images/logoST.jpg')}}" class="h-64px" alt="">
            </div>
            <h5 class="mb-0">Vui lòng thiết lập mật khẩu cho tài khoản: {{$email}}</h5>
        </div>

        @error('message')
        <label id="message-error" class="validation-error-label text-danger w-100 text-center" for="basic">{{ $message }}</label>
        @enderror
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="password" type="{{$showPassword?'type':'password'}}" class="form-control @error('password') is-invalid @enderror" placeholder="•••••••••••" id="password" name="password" value="{{ old('password') }}"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-lock text-muted"></i>
                </div>
                @error('password')
                <label id="error-password" class="validation-error-label text-danger" for="password">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu</label>
            <div class="form-control-feedback form-control-feedback-start">
                <input wire:model.live="confirmPassword" type="{{$showPassword?'type':'password'}}" class="form-control @error('confirmPassword') is-invalid @enderror" placeholder="•••••••••••" id="password" name="password" value="{{ old('password') }}"/>
                <div class="form-control-feedback-icon">
                    <i class="ph-lock text-muted"></i>
                </div>
                @error('confirmPassword')
                <label id="error-confirmPassword" class="validation-error-label text-danger" for="confirmPassword">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="showPasswords" wire:model.live="showPassword">
            <label class="form-check-label" for="showPasswords">Hiển thị mật khẩu</label>
        </div>

        <div class="my-3">
            <button type="submit" class="btn btn-primary w-100">
                <span wire:loading wire:target="SetPasswordUser" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                Thiết lập
            </button>
        </div>
    </form>
</div>
