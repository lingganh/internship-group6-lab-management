<div>
    <div class="row">
        <div class="col-lg-3 col-sm-0"></div>
        <div class="col-lg-6 col-sm-12">
            <div class="card" >
                <div class="card-header">
                    <h4 class="mb-0 text-uppercase">Ứng dụng Authenticator</h4>
                </div>
                {{session('status')}}

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
                        <div>
                            <div class="col-form-label">Quản lý cài đặt xác thực hai yếu tố</div>
                            @if(! $this->user->two_factor_confirmed_at)
                                <div class="btn btn-danger disabled">Chưa thiết lập </div>
                            @else
                                <div class="btn btn-success"> Đang bật 2FA </div>
                            @endif
                            <div class="col-form-label">
                                Khi bật xác thực hai yếu tố, bạn sẽ được nhắc nhập mã PIN an toàn, ngẫu nhiên khi đăng nhập. Bạn có thể lấy mã PIN này từ ứng dụng hỗ trợ TOTP trên điện thoại.
                            </div>
                        </div>
                        <img src="{{asset('assets/images/2SV_scene_authenticator_v2_light_7c96b78219755e04538db62d7523eca7.svg')}}" alt="">
                    </div>
                    @if($this->user->two_factor_confirmed_at)
                        <div class="border p-3 mb-3 rounded">
                            <strong>Mã khôi phục 2FA:</strong>
                            <p>Mã khôi phục cho phép bạn lấy lại quyền truy cập nếu bị mất thiết bị 2FA. Hãy lưu trữ chúng trong trình quản lý mật khẩu an toàn.</p>

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-light" @click="$wire.ShowRecoveryCodes">
                                    <span wire:loading wire:target="ShowRecoveryCodes" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                                    @if(!$showRecoveryCodes)
                                        <i wire:loading.remove wire:target="ShowRecoveryCodes" class="ph-eye"></i> Xem mã khôi phục
                                    @else
                                        <i wire:loading.remove class="ph-eye-slash"></i> Ẩn mã khôi phục
                                    @endif
                                </button>
                                @if($showRecoveryCodes)
                                    <button class="btn btn-info" @click="$wire.RegenerateCodes">
                                        <span wire:loading wire:target="RegenerateCodes" class="btn-loading"><i class="ph-spinner-gap animate-spin"></i></span>
                                        <i wire:loading.remove wire:target="RegenerateCodes" class="ph-arrows-clockwise"></i>
                                        Tạo lại mã
                                    </button>
                                @endif
                            </div>

                            <div style="min-height: 20px;"> @if($showRecoveryCodes)
                                    <div
                                        wire:transition.scale.origin.top.duration.300ms
                                        class="alert alert-info mt-2 shadow-sm border-info"
                                    >
                                        {{-- Hiển thị dạng lưới 2 cột, font chữ monospace (như code) --}}
                                        <div class="row g-2 mb-3 font-monospace fw-bold text-dark">
                                            @foreach($this->user->recoveryCodes() as $code)
                                                <div class="col-6">
                                                    {{ $code }}
                                                </div>
                                            @endforeach
                                        </div>

                                        <hr>

                                        <p class="mb-0 small text-muted">
                                            <i class="ph-info"></i>
                                            Mỗi mã khôi phục chỉ được sử dụng một lần để truy cập tài khoản của bạn và sẽ bị xóa sau khi sử dụng. Nếu bạn cần thêm, hãy nhấp vào mục <strong>Tạo lại mã</strong> ở trên.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div>
                        @if($this->user->two_factor_secret && $this->user->two_factor_confirmed_at)
                            <button class="btn btn-danger" wire:click="openModalConfirmDisable"><i class="ph-shield-slash"></i> Tắt 2FA</button>
                        @else
                            {{-- Chưa bật hoặc đang bật dở -> Hiện nút Thiết lập --}}
                            @if(!$showingQrCode && !$showingOtpInput)
                                <button wire:click="enableTwoFactor" class="btn btn-primary">
                                    <i class="ph-shield-check"></i> Thiết lập 2FA
                                </button>
                            @else
                                <button class="btn btn-secondary disabled">Đang thiết lập...</button>
                            @endif
                        @endif
                    </div>
                    <div class="col-form-label">Trước tiên, hãy tải ứng dụng Google Authenticator xuống từ
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" >Cửa hàng Google Play</a> hoặc <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" >App Store trên thiết bị iOS.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL 1: QUÉT QR CODE --}}
    @if($showingQrCode)
        {{-- Thêm lớp style hiển thị đè lên màn hình (giả lập modal active) --}}
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog" style="top:10%; width: 400px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thiết lập ứng dụng xác thực</h5>
                        {{-- Nút X gọi hàm cancelSetup --}}
                        <button type="button" class="btn-close" wire:click="cancelShowQr"></button>
                    </div>
                    <div class="modal-body">
                        <div class="px-2 d-flex flex-column justify-between">
                            <ul class="ps-3 text-sm">
                                <li>Mở ứng dụng Authenticator, nhấn dấu <strong>+</strong></li>
                                <li>Chọn <strong>Quét mã QR</strong></li>
                            </ul>

                            {{-- Hiển thị QR --}}
                            <div class="d-flex justify-content-center p-3 bg-light border rounded shadow-sm mb-3">
                                {!! $this->user->twoFactorQrCodeSvg() !!}
                            </div>

                            <div class="text-center text-muted content-divider mb-3">
                                <span class="px-2">hoặc, nhập mã thủ công</span>
                            </div>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" readonly value="{{ decrypt($this->user->two_factor_secret) }}">
                                <button class="btn btn-light border"
                                        onclick="copySecret(this, '{{ decrypt($this->user->two_factor_secret) }}')">
                                    Copy
                                </button>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-danger" wire:click="cancelShowQr">Hủy</button>
                                <button class="btn btn-primary" wire:click="openOtpModal">Tiếp tục</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- MODAL 2: NHẬP OTP XÁC NHẬN --}}
    @if($showingOtpInput)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog" style="top:10%; width: 400px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác minh Mã Xác thực</h5>
                        <button type="button" class="btn-close" wire:click="cancelShowQr"></button>
                    </div>
                    <div class="modal-body">
                        <div class="max-w-xs mx-auto">
                            <div class="mb-3 text-center">Nhập mã 6 chữ số từ ứng dụng xác thực của bạn.</div>
                            <input type="number"
                               wire:model="code"
                               wire:keydown.enter="confirmTwoFactor"
                               class="form-control text-center fs-3 letter-spacing-2 mb-2 @error('code') is-invalid @enderror"
                               placeholder="######" oninput="this.value = this.value.slice(0, 6)"
                            >

                            @error('code')
                            <span class="text-danger d-block mb-2">{{ $message }}</span>
                            @enderror

                            <div class="mt-4 d-flex gap-2">
                                <button wire:click="confirmTwoFactor" class="btn btn-primary flex-fill">
                                    Xác nhận
                                </button>
                                <button wire:click="cancelShowOtp"
                                        class="btn btn-outline-secondary">
                                    Quay lại
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <script>
        function copySecret(btn, text) {
            navigator.clipboard.writeText(text).then(() => {
                const icon = btn.querySelector('i');

                icon.classList.remove('ph-files');
                icon.classList.add('ph-check');

                // Đổi lại icon sau 1.5s (tuỳ ý)
                setTimeout(() => {
                    icon.classList.remove('ph-check');
                    icon.classList.add('ph-files');
                }, 10000);
            });
        }
    </script>


</div>
