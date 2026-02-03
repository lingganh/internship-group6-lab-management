<x-admin-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Danh sách nhóm NCKH
                    </h4>
                </div>

            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        {{--                        <a href="{{route('admin.users.index')}}" class="breadcrumb-item">Người dùng</a>--}}
                        <span class="breadcrumb-item active">Danh sách nhóm NCKH</span>
                    </div>
                </div>

            </div>
        </div>
    </x-slot>
    <div class="content">
        <livewire:admin.groups.index/>
    </div>
</x-admin-layout>
