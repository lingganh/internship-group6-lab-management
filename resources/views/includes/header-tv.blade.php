<div class="navbar navbar-st navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
    <div class="container-fluid">
        <div class="d-flex d-lg-none me-2">
            <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                <i class="ph-list"></i>
            </button>
        </div>

        <div class="navbar-brand flex-1 flex-lg-0 ms-xl-5">
            <a href="{{ route('lab.calendar.tv') }}" class="d-inline-flex align-items-center">
                <img class="w-40px h-40px" src="{{ asset('assets/images/login.png') }}" alt="">
            </a>
            <span class="d-none d-lg-inline-block mx-lg-2"
                style="text-transform: uppercase; font-weight: bold; font-size: 16px; color: #fff">Hệ thống quản lý
                phòng lab</span>
        </div>



        <ul class="nav flex-row justify-content-end order-1 order-lg-2 align-items-center">
            <li class="nav-item ms-lg-2">
                <a href="{{ route('events.calendar') }}" class="navbar-nav-link align-items-center rounded-pill p-1">
                    <div class="status-indicator-container">
                        <i class="ph-newspaper "></i>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">Sự kiện</span>
                </a>
            </li>
            <li class="nav-item ms-lg-2">
                <a href="{{ route('lab.calendar.tv') }}" class="navbar-nav-link align-items-center rounded-pill p-1">
                    <div class="status-indicator-container">
                        <i class="ph-calendar "></i>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">Lịch phòng LAB</span>
                </a>
            </li>

            

             
                    
        </ul>
    </div>
</div>
