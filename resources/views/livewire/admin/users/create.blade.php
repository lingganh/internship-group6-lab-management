<div class="row">
    <div class="col-md-9 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-info"></i>
                Thông tin người dùng
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="fullName" class="col-form-label" wire:ignore>
                            Họ và Tên: <span class="required">*</span>
                        </label>
                        <input wire:model.live="fullName" type="text" id="fullName" class="form-control @error('fullName') is-invalid @enderror">
                        @error('fullName')
                        <label id="error-fullName" class="validation-error-label text-danger"
                               for="fullName">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="role" class="col-form-label">
                            Vai trò: <span class="required">*</span>
                        </label>
                        <select id="selectRole" class="form-control select @error('selectRole') is-invalid @enderror" wire:model.live="roleName">
                            @foreach($roles as $value => $name)
                                <option value="{{$value}}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <label id="error-roleName" class="validation-error-label text-danger"
                               for="roleName">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="code" class="col-form-label" wire:ignore>
                            Mã GV: <span class="required">*</span>
                        </label>
                        <input wire:model.live="code" type="text" id="code" class="form-control @error('code') is-invalid @enderror">
                        @error('code')
                        <label id="error-code" class="validation-error-label text-danger"
                               for="code">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="email" class="col-form-label">
                            Email: <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <input wire:model.live="email" type="text" id="email"
                                   class="form-control @error('email') is-invalid @enderror">
                        </div>
                        @error('email')
                        <label id="error-username" class="validation-error-label text-danger"
                               for="username">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="className" class="col-form-label" wire:ignore>
                            Bộ môn:
                        </label>
                        <select id="selectDepartment" class="form-control select @error('selectDepartment') is-invalid @enderror" wire:model.live="department">
                            <option value="">Chọn bộ môn...</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('className')
                        <label id="error-className" class="validation-error-label text-danger"
                               for="className">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="phone" class="col-form-label">
                            Số điện thoại:
                        </label>
                        <div class="input-group">
                            <input wire:model.live="phone" type="text" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror">
                        </div>
                        @error('phone')
                        <label id="error-phone" class="validation-error-label text-danger"
                               for="phone">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="dateOfBirdth" class="col-form-label" wire:ignore>
                            Ngày sinh:
                        </label>
                        <input wire:model.live="dateOfBirdth" type="date" id="dateOfBirdth" class="form-control @error('dateOfBirdth') is-invalid @enderror">
                        @error('dateOfBirdth')
                        <label id="error-dateOfBirdth" class="validation-error-label text-danger"
                               for="dateOfBirdth">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="gender" class="col-form-label">
                            Giới tính:
                        </label>
                        <select id="selectRole" class="form-control select @error('selectRole') is-invalid @enderror" wire:model.live="gender">
                            <option value="" >Chọn giới tính...</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ"> Nữ</option>
                        </select>
                        @error('gender')
                        <label id="error-gender" class="validation-error-label text-danger"
                               for="gender">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
{{--                    <div class="col-6">--}}
{{--                        <label for="avatar" class="col-form-label">--}}
{{--                            Ảnh đại diện:--}}
{{--                        </label>--}}
{{--                        <input wire:model="avatar" type="file" id="avatar" class="form-control">--}}
{{--                        @error('avatar')--}}
{{--                        <label id="error-avatar" class="validation-error-label text-danger"--}}
{{--                               for="avatar">{{ $message }}</label>--}}
{{--                        @enderror--}}
{{--                        @if ($avatar)--}}
{{--                            @dd($avatar->temporaryUrl())--}}
{{--                        @dd($avatar)--}}
{{--                            @if (Str::startsWith($avatar->getMimeType(), 'image/'))--}}
{{--                                <div class="avatar_result">--}}
{{--                                    <div class="file-uploaded">--}}
{{--                                        <img src="{{ $avatar->temporaryUrl() }}" alt="Preview file" style="max-height: 230px">--}}
{{--                                    </div>--}}
{{--                                    <div class="remove-thumbnail" wire:click="removeAvatar">X</div>--}}

{{--                                </div>--}}
{{--                            @endif--}}
{{--                        @endif--}}
{{--                    </div>--}}
                    <div class="col-6">
                        <label for="password" class="col-form-label">
                            Mật khẩu: <span class="required">*</span>
                        </label>
                        <input wire:model.live="password" type="text" id="password" class="form-control @error('password') is-invalid @enderror">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="setPassword" wire:model.live="setPassword">
                            <label class="form-check-label" for="setPassword">Mật khẩu mặc định: @FITA-2015$</label>
                        </div>
                        @error('password')
                        <label id="error-password" class="validation-error-label text-danger"
                               for="password">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-gear-six"></i>
                Hành động
            </div>
            <div class="card-body d-flex align-items-center gap-1 flex-wrap">
                <button wire:loading.remove wire:target="save" class="btn btn-primary" @click="$wire.save"><i class="ph-floppy-disk"></i> Cập nhật</button>
                <button wire:loading wire:target="save" class="btn btn-primary" @click="$wire.save"><i class="ph-spinner-gap animate-spin"></i> Cập nhật</button>

                <a href="{{route('admin.users.index')}}" type="button" class="btn btn-warning"><i class="ph-arrow-counter-clockwise"></i> Trở lại</a>
            </div>
        </div>
    </div>
</div>

