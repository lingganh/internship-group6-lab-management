@php
    use App\Enums\User\UserRoleEnum;
@endphp
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section" >
            <div class="sidebar-section-body d-flex justify-content-center">
                <h5 class="sidebar-resize-hide flex-grow-1 my-auto">Hệ thống quản lý</h5>

                <div>
                    <button type="button"
                            class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button type="button"
                            class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /sidebar header -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link">
                        <i class="ph-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Lịch - thời khoá biểu</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link {{ request()->routeIs('admin.coming-soon') ? 'active' : '' }}">
                        <i class="ph-calendar-plus"></i>
                        <span>Đăng ký lịch</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link {{ request()->routeIs('admin.coming-soon') ? 'active' : '' }}">
                        <i class="ph-calendar"></i>
                        <span>Lịch đã đăng ký</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('admin.approval')}}"
                       class="nav-link ">
                        <i class="ph-calendar-check"></i>
                        <span>Duyệt lịch đăng ký</span>
                    </a>
                </li>

                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Nhóm - Hoạt động</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.groups.index')}}"
                       class="nav-link">
                        <i class="ph-users-three"></i>
                        <span>Nhóm NCKH</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.lab-diary')}}"
                       class="nav-link">
                        <i class="ph-note-blank"></i>
                        <span>Nhật ký sử dụng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link">
                        <i class="ph-activity"></i>
                        <span>Hoạt động nhóm</span>
                    </a>
                </li>
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Báo cáo - Thống kê</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link">
                        <i class="ph-chart-bar"></i>
                        <span>Báo cáo - Thống kê</span>
                    </a>
                </li>

                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Quản lý hệ thống</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.users.index')}}"
                       class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <i class="ph-user"></i>
                        <span>Người dùng</span>
                    </a>
                </li>
                                <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                       class="nav-link {{ request()->routeIs('admin.coming-soon') ? 'active' : '' }}">
                        <i class="ph-chart-bar"></i>
                        <span>Thiết bị</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
