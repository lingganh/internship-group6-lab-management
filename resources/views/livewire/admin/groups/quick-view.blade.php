<div>
    @if($user)
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <label for="fullName" class="col-form-label" wire:ignore>
                    Họ và Tên: {{$user->full_name??''}}
                </label>
            </div>
            <div class="col-6">
                <label for="role" class="col-form-label">
                    Vai trò: {!! $user->role_text !!}
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label for="code" class="col-form-label" wire:ignore>
                    Mã SV/GV: {{$user->code??''}}
                </label>
            </div>
            <div class="col-6">
                <label for="email" class="col-form-label">
                    Email: {{$user->email??''}}
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label for="className" class="col-form-label" wire:ignore>
                    Lớp: {{$user->class_name??''}}
                </label>
            </div>
            <div class="col-6">
                <label for="phone" class="col-form-label">
                    Số điện thoại: {{$user->phone??''}}
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label for="dateOfBirdth" class="col-form-label" wire:ignore>
                    Ngày sinh: {{ optional($user)->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d-m-Y') : '' }}
                </label>
            </div>
            <div class="col-6">
                <label for="gender" class="col-form-label">
                    Giới tính: {{$user->gender ??''}}
                </label>
            </div>
        </div>
    </div>
    @endif
</div>
