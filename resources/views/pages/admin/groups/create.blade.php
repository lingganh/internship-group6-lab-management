<x-admin-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Tạo nhóm NCKH mới
                    </h4>
                </div>

            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <a href="{{route('admin.groups.index')}}" class="breadcrumb-item">Danh sách nhóm NCKH</a>
                        <span class="breadcrumb-item active">Tạo nhóm NCKH mới</span>
                    </div>
                </div>

            </div>
        </div>
    </x-slot>
    <div class="content">
        <livewire:admin.groups.create/>
    </div>
</x-admin-layout>
