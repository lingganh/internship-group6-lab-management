<div class="row">
    <div class="col-lg-3 col-sm-0"></div>
    <div class="col-lg-6 col-sm-12">
        <div class="card" >
            <div class="card-header">
                <h4 class="mb-0 text-uppercase">Xác minh mật khẩu</h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label for="password" class="col-form-label">
                            Nhập mật khẩu:
                        </label>
                        <input wire:model.live="password" type="{{$showPassword?'text':'password'}}" id="password" class="form-control" placeholder="Nhập mật khẩu mới">
                        @error('password')
                        <label id="error-password" class="validation-error-label text-danger"
                               for="password">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-check  mt-3">
                    <input type="checkbox" class="form-check-input" id="showPasswords" wire:model.live="showPassword">
                    <label class="form-check-label" for="showPasswords">Hiển thị mật khẩu</label>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-3 me-3">
                <button class="btn btn-primary" @click="$wire.confirmPassword" wire:loading.remove wire:target="confirmPassword">
                    <i class="ph-check"></i> Xác nhận
                </button>
                <button class="btn btn-primary" @click="$wire.confirmPassword" wire:loading wire:target="confirmPassword">
                    <i class="ph-spinner-gap animate-spin"></i>
                    Đang xử lý...
                </button>

            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-0"></div>

</div>
