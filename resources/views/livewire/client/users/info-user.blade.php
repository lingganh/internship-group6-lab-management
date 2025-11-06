<div>
    <div class="row">
        <div class="col-lg-3 col-sm-0"></div>
        <div class="col-lg-6 col-sm-12">
            <div class="card" >
                <div class="card-header">
                    <h4 class="mb-0 text-uppercase">Thông tin tài khoản</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="" class="col-form-label" wire:ignore>
                                Mã SV/GV: {{$code}}
                            </label>
                        </div>
                        <div class="col-6">
                            <label for="" class="col-form-label">
                                Vai trò: {!! auth()->user()->role_text??'' !!}
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label for="fullName" class="col-form-label" wire:ignore>
                                Họ và Tên: <span class="required">*</span>
                            </label>
                            <input wire:model.live="fullName" type="text" id="fullName" class="form-control">
                            @error('fullName')
                            <label id="error-fullName" class="validation-error-label text-danger"
                                   for="fullName">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="className" class="col-form-label" wire:ignore>
                                Lớp:
                            </label>
                            <input wire:model.live="className" type="text" id="className" class="form-control">
                            @error('className')
                            <label id="error-className" class="validation-error-label text-danger"
                                   for="className">{{ $message }}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="phone" class="col-form-label">
                                Số điện thoại:
                            </label>
                            <div class="input-group">
                                <input wire:model.live="phone" type="text" id="phone"
                                       class="form-control">
                            </div>
                            @error('phone')
                            <label id="error-phone" class="validation-error-label text-danger"
                                   for="phone">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="email" class="col-form-label">
                                Email: <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input wire:model.live="email" type="text" id="email"
                                       class="form-control">
                            </div>
                            @error('email')
                            <label id="error-username" class="validation-error-label text-danger"
                                   for="username">{{ $message }}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="dateOfBirdth" class="col-form-label" wire:ignore>
                                Ngày sinh:
                            </label>
                            <input wire:model.live="dateOfBirdth" type="date" id="dateOfBirdth" class="form-control">
                            @error('dateOfBirdth')
                            <label id="error-dateOfBirdth" class="validation-error-label text-danger"
                                   for="dateOfBirdth">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="gender" class="col-form-label">
                                Giới tính:
                            </label>
                            <select id="selectRole" class="form-control select" wire:model.live="gender">
                                <option value="" @if($gender!=null) disabled @endif >Chọn giới tính...</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ"> Nữ</option>
                            </select>
                            @error('gender')
                            <label id="error-gender" class="validation-error-label text-danger"
                                   for="gender">{{ $message }}</label>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-3 me-3">
                    <button class="btn btn-primary" @click="$wire.openModel"><i class="ph-floppy-disk"></i> Cập nhật</button>

                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-0"></div>

    </div>
</div>
