<div>

            <div class="row">
                <div class="col-12">
                    <label for="newPassword" class="col-form-label" wire:ignore>
                        Mật khẩu mới: <span class="required">*</span>
                    </label>
                    <input wire:model.live="newPassword" type="{{$showPassword?'text':'password'}}" id="newPassword" class="form-control" placeholder="Nhập mật khẩu mới">
                    @error('newPassword')
                    <label id="error-newPassword" class="validation-error-label text-danger"
                           for="newPassword">{{ $message }}</label>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="confirmNewPassword" class="col-form-label" wire:ignore>
                        Nhập lại mật khẩu:
                    </label>
                    <input wire:model.live="confirmNewPassword" type="{{$showPassword?'text':'password'}}" id="confirmNewPassword" class="form-control" placeholder="Nhập lại mật khẩu mới">
                    @error('confirmNewPassword')
                    <label id="error-confirmNewPassword" class="validation-error-label text-danger"
                           for="confirmNewPassword">{{ $message }}</label>
                    @enderror
                </div>
            </div>
            <div class="form-check  mt-3">
                <input type="checkbox" class="form-check-input" id="showPasswords" wire:model.live="showPassword">
                <label class="form-check-label" for="showPasswords">Hiển thị mật khẩu</label>
            </div>

        <div class="d-flex justify-content-center mb-3 me-3">
            <button class="btn btn-primary" @click="$wire.updatePassword" wire:loading.remove wire:target="updatePassword">
                <i class="ph-floppy-disk"></i> Lưu
            </button>
            <button class="btn btn-primary" @click="$wire.updatePassword" wire:loading wire:target="updatePassword">
                <i class="ph-spinner-gap animate-spin"></i>
                Đang lưu
            </button>
        </div>
</div>
