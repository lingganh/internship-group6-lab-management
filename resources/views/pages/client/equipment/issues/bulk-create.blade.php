<x-client-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content">
                <div class="page-title d-flex">
                    <h4 class="mb-0">
                        <i class="ph-warning-circle me-2"></i>
                        Tạo báo hỏng nhiều thiết bị
                    </h4>
                </div>
            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{ route('home') }}" class="breadcrumb-item">
                            <i class="ph-house"></i>
                        </a>
                        <span class="breadcrumb-item active">
                            Tạo báo hỏng nhiều thiết bị
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="content pt-3">
        <livewire:client.equipment-issues.bulk-create />
    </div>
</x-client-layout>
