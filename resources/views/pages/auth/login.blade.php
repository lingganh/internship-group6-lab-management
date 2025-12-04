<x-auth-layout>
    <div class="content login-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row login-row">
                    <div class="col-xl-6">
                        <div class="login-image-wrapper">
                            <img class="login-image" src="{{asset('assets/images/login.png')}}" alt="login">
                            <div class="line"></div>
                            <div class="login-note text-muted">
                                Lưu ý: Hệ thống quản lý lịch sử dụng phòng máy dành cho cán bộ, sinh viên, người phụ trách các nhóm sử dụng
                                vậy nếu đã đăng nhập bằng tài khoản Microsoft mà chưa thiết lập mật khẩu thì vui lòng chọn quên mật khẩu để thiết lập lại.
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-6 ">
                        <livewire:auth.form-login/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
