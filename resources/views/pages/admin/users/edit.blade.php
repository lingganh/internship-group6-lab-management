<x-admin-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Người dùng - <span class="fw-normal">Danh sách người dùng</span>
                    </h4>

                    <a href="#page_header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <a href="{{route('admin.users.index')}}" class="breadcrumb-item">Danh sách người dùng</a>
                        <span class="breadcrumb-item active">Chỉnh sửa người dùng</span>
                    </div>

                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </x-slot>

    <div class="content">
        <livewire:admin.users.edit :userId="$id"/>
    </div>


</x-admin-layout>
