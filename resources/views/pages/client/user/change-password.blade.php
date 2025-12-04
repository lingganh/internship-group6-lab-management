<x-client-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('home')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <span class="breadcrumb-item active">Đổi mật khẩu  </span>
                    </div>
                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </x-slot>
    <div class="content">
        <livewire:client.users.change-password/>
    </div>
</x-client-layout>
