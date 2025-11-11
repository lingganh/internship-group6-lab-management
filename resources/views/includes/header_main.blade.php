<div class="navbar navbar-st navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
    <div class="container-fluid">
        <div class="d-flex d-lg-none me-2">
            <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                <i class="ph-list"></i>
            </button>
        </div>

        <div class="navbar-brand flex-1 flex-lg-0 ms-xl-5">
            <a href="{{route('home')}}" class="d-inline-flex align-items-center">
                <img class="w-40px h-40px" src="{{ asset('assets/images/login.png') }}" alt="">
            </a>
            <span class="d-none d-lg-inline-block mx-lg-2" style="text-transform: uppercase; font-weight: bold; font-size: 16px; color: #fff">Hệ thống quản lý phòng lab</span>
        </div>



        <ul class="nav flex-row justify-content-end order-1 order-lg-2 align-items-center">
            <li class="nav-item ms-lg-2">
                <a href="{{route('events.calendar')}}" class="navbar-nav-link align-items-center rounded-pill p-1">
                    <div class="status-indicator-container">
                        <i class="ph-newspaper "></i>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">Sự kiện</span>
                </a>
            </li>
            <li class="nav-item ms-lg-2">
                <a href="{{route('home')}}" class="navbar-nav-link align-items-center rounded-pill p-1">
                    <div class="status-indicator-container">
                        <i class="ph-calendar "></i>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">Lịch phòng LAB</span>
                </a>
            </li>

            @if(auth()->check())
                <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                    <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                        <div class="status-indicator-container">
                            <img src="{{ Avatar::create(auth()->user()->full_name ?? auth()->user()->full_name)->toBase64() }}" class="w-32px h-32px rounded-pill" alt="">
                            <span class="status-indicator bg-success"></span>
                        </div>
                        <span class="d-none d-lg-inline-block mx-lg-2">{{auth()->user()->full_name ?? auth()->user()->full_name}}</span>
{{--                        <div class="status-indicator-container">--}}
{{--                            <img src="{{ asset('assets\images\default-user-image.png')}}" class="w-32px h-32px rounded-pill" alt="">--}}
{{--                        </div>--}}
{{--                        <span class="d-none d-lg-inline-block mx-lg-2">Phong</span>--}}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{route('admin.dashboard')}}" class="dropdown-item">
                                <i class="ph-wrench me-2"></i>
                                Quản trị hệ thống
                            </a>
                        @endif
                        <a href="{{route('admin.coming-soon')}}" class="dropdown-item">
                            <i class="ph-calendar me-2"></i>
                            Lịch đã đăng ký
                        </a>
                        <a href="{{route('client.info-user')}}" class="dropdown-item">
                            <i class="ph-user me-2"></i>
                            Tài khoản
                        </a>
                        <a href="{{route('client.change-password')}}" class="dropdown-item">
                            <i class="ph-lock-key me-2"></i>
                            Đổi mật khẩu
                        </a>
                        <div class="dropdown-divider"></div>

                        <form action="{{route('handleLogout')}}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="ph-sign-out me-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </li>
            @else
                <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                    <a href="{{route('login')}}" class="navbar-nav-link align-items-center rounded-pill p-1">
                        <div class="status-indicator-container">
                            <img src="{{ asset('assets\images\default-user-image.png')}}" class="w-32px h-32px rounded-pill" alt="">
                        </div>
                        <span class="d-none d-lg-inline-block mx-lg-2">Đăng nhập</span>
                    </a>

                </li>
            @endif
        </ul>
    </div>
</div>
