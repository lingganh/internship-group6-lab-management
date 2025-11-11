<div>
    <div class="row">
        <div class="col-lg-3 col-sm-0"></div>
        <div class="col-lg-6 col-sm-12">
            <div class="card" >
                <div class="card-header">
                    <h4 class="mb-0 text-uppercase">Đổi mật khẩu</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="currentPassword" class="col-form-label" wire:ignore>
                                Mật khẩu hiện tại: <span class="required">*</span>
                            </label>
                            <input wire:model.live="currentPassword" type="{{$showPassword?'text':'password'}}" id="currentPassword" class="form-control" placeholder="Nhập mật khẩu hiện tại">
                            @error('currentPassword')
                            <label id="error-currentPassword" class="validation-error-label text-danger"
                                   for="currentPassword">{{ $message }}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="newPassword" class="col-form-label" wire:ignore>
                                Mật khẩu mới: <span class="required">*</span>
                            </label>
                            <input wire:model.live="newPassword" type="{{$showPassword?'text':'password'}}" id="newPassword" class="form-control" placeholder="Nhập mật khẩu mới">
                            @error('newPassword')
                            <label id="error-newPassword" class="validation-error-label text-danger"
                                   for="newPassword">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-6">
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
                </div>
                <div class="d-flex justify-content-center mb-3 me-3">
                    <button class="btn btn-primary" @click="$wire.openModel" wire:loading.remove wire:target="openModel">
                        <i class="ph-floppy-disk"></i> Lưu
                    </button>
                    <button class="btn btn-primary" @click="$wire.openModel" wire:loading wire:target="openModel">
                        <i class="ph-spinner-gap animate-spin"></i>
                        Đang lưu
                    </button>

                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-0"></div>

    </div>
</div>
