<x-admin-layout>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Phiếu ý kiến sử dụng phòng
                    </h4>

                </div>
            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                            <i class="ph-house"></i>
                        </a>
                        <a href="{{ route('admin.equipment-issue-requests.index') }}" class="breadcrumb-item">
                            Ý kiến sử dụng phòng
                        </a>
                        <span class="breadcrumb-item active">
                            Phiếu #{{ $issueRequest->id }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="content">
        <livewire:admin.equipment-issue-requests.show :requestId="$issueRequest->id" />
    </div>
</x-admin-layout>
