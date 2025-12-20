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
                        <label for="lastLoginAt" class="col-form-label" wire:ignore>
                            Trạng thái tài khoản:
                            {!! str_replace('class="', 'class="fs-14 ', $user->user_status) !!}
                            -
                            @if($user->email_verified_at===null && $user->sso_id === null)
                                <span class="badge fs-14 text-danger">Chưa xác minh email</span>
                            @elseif($user->email_verified_at===null && $user->sso_id !== null)
                                <span class="badge fs-14 text-info">Chưa thiết lập mật khẩu</span>
                            @elseif($user->email_verified_at!==null && $user->sso_id === null)
                                <span class="badge fs-14 text-info">Chưa thiết lập SSO</span>
                            @else
                                <span class="badge fs-14 text-success ">Bình thường</span>
                            @endif
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
                        <label for="role" class="col-form-label">
                            Vai trò: <span class="required">*</span>
                        </label>
                        <select id="selectRole" class="form-control select" wire:model.live="roleName" @if($userId == auth()->user()->id) disabled @endif>
                            <option value="" disabled>Chọn vai trò ...</option>
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
                        <input wire:model.live="code" type="text" id="code" class="form-control">
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
                        <label for="className" class="col-form-label" wire:ignore>
                            Bộ môn:
                        </label>
                        <select id="selectDepartment" class="form-control select" wire:model.live="department">
                            <option value="" @if($department!=null) disabled @endif >Chọn bộ môn...</option>
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
                                   class="form-control">
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
                <div class="row">
                    <div class="col-6">
                        <label for="lastLoginAt" class="col-form-label" wire:ignore>
                            Lần đăng nhập gần nhất:
                        </label>
                        <input wire:model="lastLoginAt" type="text" id="lastLoginAt" class="form-control" disabled>
                        @error('lastLoginAt')
                        <label id="error-lastLoginAt" class="validation-error-label text-danger"
                               for="lastLoginAt">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="createUserAt" class="col-form-label">
                            Ngày tạo tài khoản:
                        </label>
                        <div class="input-group">
                            <input wire:model="createUserAt" type="text" id="createUserAt"
                                   class="form-control" disabled>
                        </div>
                        @error('createUserAt')
                        <label id="error-createUserAt" class="validation-error-label text-danger"
                               for="createUserAt">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                @if($user->two_factor_confirmed_at)
                    <div class="row">
                        <label for="lastLoginAt" class="col-form-label" wire:ignore>
                            Mã khôi phục tài khoản:
                        </label>
                        <div class="alert shadow-sm border-info">
                            <div class="row g-2 fw-bold text-dark">
                                @foreach($user->recoveryCodes() as $code)
                                    <div class="col-6">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
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
                <button wire:loading.remove wire:target="update" class="btn btn-primary" @click="$wire.update"><i class="ph-floppy-disk"></i> Cập nhật</button>
                <button wire:loading wire:target="update" class="btn btn-primary" @click="$wire.update"><i class="ph-spinner-gap animate-spin"></i> Cập nhật</button>
{{--                @if($user->email_verified_at===null && $user->sso_id === null)--}}
{{--                    <button wire:loading.remove wire:target="openModelDelete" class="btn btn-danger" @click="$wire.openModelDelete"><i class="ph-trash"></i> Xóa</button>--}}
{{--                    <button wire:loading wire:target="openModelDelete" class="btn btn-danger" @click="$wire.openModelDelete"><i class="ph-spinner-gap animate-spin"></i> Xóa</button>--}}
{{--                @else--}}
    {{--                    <a wire:loading.remove wire:target="changePassword" @click="$wire.changePassword" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-password" type="button" class="btn btn-info"><i class="ph-lock"></i> Đổi mật khẩu </a>--}}
    {{--                    <a wire:loading wire:target="changePassword" @click="$wire.changePassword" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-password" type="button" class="btn btn-info"><i class="ph-spinner-gap animate-spin"></i> Đổi mật khẩu </a>--}}
{{--                @endif--}}
                @if($user->status===\App\Enums\UserStatus::Archived->value)
                    <button wire:loading.remove wire:target="openRearchiveModal" @click="$wire.openRearchiveModal" class="btn btn-info"><i class="ph-arrow-arc-left"></i> Khôi phục tài khoản </button>
                    <button wire:loading wire:target="openRearchiveModal" class="btn btn-info"><i class="ph-spinner-gap animate-spin"></i> Khôi phục tài khoản </button>
                @else
                    @if($user->status===\App\Enums\UserStatus::Pending->value)
                        <button wire:loading.remove wire:target="openApproveModal" @click="$wire.openApproveModal" class="btn btn-info"><i class="ph-checks"></i> Duyệt tài khoản </button>
                        <button wire:loading wire:target="openApproveModal" class="btn btn-info"><i class="ph-spinner-gap animate-spin"></i> Duyệt tài khoản </button>
                    @endif
                    @if($user->status===\App\Enums\UserStatus::Approved->value && $user->id !== auth()->id())
                        <button wire:loading.remove wire:target="openArchiveModal" @click="$wire.openArchiveModal" class="btn btn-secondary"><i class="ph-archive"></i> Lưu trữ </button>
                        <button wire:loading wire:target="openArchiveModal" class="btn btn-secondary"><i class="ph-spinner-gap animate-spin"></i> Lưu trữ </button>
                        <a wire:loading.remove wire:target="changePassword" @click="$wire.changePassword" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-password" type="button" class="btn btn-info"><i class="ph-lock"></i> Đổi mật khẩu </a>
                        <a wire:loading wire:target="changePassword" @click="$wire.changePassword" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-password" type="button" class="btn btn-info"><i class="ph-spinner-gap animate-spin"></i> Đổi mật khẩu </a>
                    @endif
                    @if($user->email_verified_at===null && $user->sso_id === null)
                        <button wire:loading.remove wire:target="openModelDelete" class="btn btn-danger" @click="$wire.openModelDelete"><i class="ph-trash"></i> Xóa</button>
                        <button wire:loading wire:target="openModelDelete" class="btn btn-danger" @click="$wire.openModelDelete"><i class="ph-spinner-gap animate-spin"></i> Xóa</button>
                    @endif
                @endif
                <a href="{{route('admin.users.index')}}" type="button" class="btn btn-warning"><i class="ph-arrow-counter-clockwise"></i> Trở lại</a>
            </div>
        </div>
    </div>
    <x-quick-view keyId="change-password" title="Đổi mật khẩu">
        <livewire:admin.users.change-password :userId="$userId"/>
    </x-quick-view>
</div>

