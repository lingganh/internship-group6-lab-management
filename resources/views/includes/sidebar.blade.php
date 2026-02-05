@php
use App\Enums\User\UserRoleEnum;
@endphp
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section">
            <div class="sidebar-section-body d-flex justify-content-center pb-1">
                <h5 class="sidebar-resize-hide flex-grow-1 my-auto">Hệ thống quản lý </h5>
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
                    <a href="{{route('admin.dashboard')}}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ph-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Lịch - thời khoá biểu</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('lab.register') }}"
                        class="nav-link {{ request()->routeIs('lab.register') ? 'active' : '' }}">
                        <i class="ph-calendar-plus"></i>
                        <span>Đăng ký lịch</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('admin.approval') }}" class="nav-link {{ request()->routeIs('admin.approval') ? 'active' : '' }}">
                        <i class="ph-calendar-check"></i>
                        <span>Danh sách lịch</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{route('admin.approval')}}"
                class="nav-link ">
                <i class="ph-calendar-check"></i>
                <span>Lịch đã đăng ký</span>
                </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{route('admin.lab-diary')}}"
                        class="nav-link {{ request()->routeIs('admin.lab-diary') ? 'active' : '' }}">
                        <i class="ph-note-blank"></i>
                        <span>Nhật ký sử dụng</span>
                    </a>
                </li>
                  <li class="nav-item">
                    <a href="{{route('admin.event-config')}}"
                        class="nav-link {{ request()->routeIs('admin.event-config') ? 'active' : '' }}">
                        <i class="ph-sliders-horizontal"></i>
                       <span>Cấu hình lịch</span>
                    </a>
                </li>
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Nhóm - Hoạt động</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.groups.index') }}" class="nav-link {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                        <i class="ph-users-three"></i>
                        <span>Nhóm NCKH</span>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a href="{{route('admin.coming-soon')}}"
                class="nav-link">
                <i class="ph-activity"></i>
                <span>Hoạt động nhóm</span>
                </a>
                </li> --}}
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Báo cáo - Thống kê</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.report')}}"
                        class="nav-link {{ request()->routeIs('admin.report') ? 'active' : '' }}">
                        <i class="ph-chart-bar"></i>
                        <span>Báo cáo - Thống kê</span>
                    </a>
                </li>

                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Quản lý hệ thống</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="ph-user"></i>
                        <span>Người dùng</span>
                    </a>
                </li>
                {{-- Menu mới: Ý kiến sử dụng phòng  --}}
                <li class="nav-item">
                    <a href="{{ route('admin.equipment-issue-requests.index') }}"
                        class="nav-link {{ request()->routeIs('admin.equipment-issue-requests.*') ? 'active' : '' }}">
                        <i class="ph-warning-circle"></i>
                        <span>Ý kiến sử dụng phòng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.equipment.index')}}"
                        class="nav-link {{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
                        <i class="ph-desktop-tower"></i>
                        <span>Thiết bị</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.lab-setting')}}"
                        class="nav-link {{ request()->routeIs('admin.lab-setting') ? 'active' : '' }}">
                        <i class="ph-gear"></i>
                        <span>Cấu hình phòng Lab</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-main');
            if (!sidebar) return;

           //remove unfold
            sidebar.classList.remove('sidebar-main-unfold');

            // stop adding
            const observer = new MutationObserver(() => {
                if (sidebar.classList.contains('sidebar-main-unfold')) {
                    sidebar.classList.remove('sidebar-main-unfold');
                }
            });

            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>

    <style>
        /* Force disable unfold behavior */
        .sidebar-main.sidebar-main-unfold {
            width: var(--sidebar-width-collapsed) !important;
        }

        .sidebar-main.sidebar-main-unfold .sidebar-content {
            overflow: hidden !important;
        }
    </style>
</div>
