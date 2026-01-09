<div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex flex-column flex-md-row gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <h4 class="page-title mb-0">
                        Thiết bị - <span class="fw-normal">Danh sách thiết bị </span>
                    </h4>

                    <a href="#page_header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>

            <div class="page-header-content d-lg-flex flex-column flex-md-row gap-2 border-top">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <a href="{{route('equipment.index')}}" class="breadcrumb-item">Danh sách thiết bị </a>
                        <span class="breadcrumb-item active">Thêm mới thiết bị</span>
                    </div>

                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="content">
        <div class="row g-3">
            <div class="col-md-9 col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-info"></i>
                        Thêm thiết bị mới
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label for="name" class="col-form-label">Tên thiết bị: <span class="required">*</span></label>
                                <input wire:model.live="name" type="text" id="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label for="lab_id" class="col-form-label">Phòng LAB: <span class="required">*</span></label>
                                <select wire:model.live="lab_id" id="lab_id" class="form-control @error('lab_id') is-invalid @enderror">
                                    <option value="">Chọn phòng...</option>
                                    @foreach($labs as $lab)
                                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                    @endforeach
                                </select>
                                @error('lab_id')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6 col-12">
                                <label for="code" class="col-form-label">Mã thiết bị:</label>
                                <input wire:model.live="code" type="text" id="code" class="form-control @error('code') is-invalid @enderror">
                                @error('code')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label for="type" class="col-form-label">Loại thiết bị:</label>
                                <input wire:model.live="type" type="text" id="type" class="form-control @error('type') is-invalid @enderror">
                                @error('type')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6 col-12">
                                <label for="quantity" class="col-form-label">Số lượng:</label>
                                <input wire:model.live="quantity" type="number" min="0" id="quantity" class="form-control @error('quantity') is-invalid @enderror">
                                @error('quantity')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="actual_quantity" class="col-form-label">Số lượng thực:</label>
                                <input wire:model.live="actual_quantity" type="number" min="0" id="actual_quantity" class="form-control @error('actual_quantity') is-invalid @enderror">
                                @error('actual_quantity')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6 col-12">
                                <label for="purchased_date" class="col-form-label">Ngày thêm:</label>
                                <input wire:model.live="purchased_date" type="date" id="purchased_date" class="form-control @error('purchased_date') is-invalid @enderror">
                                @error('purchased_date')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label for="notes" class="col-form-label">Ghi chú:</label>
                                <textarea wire:model.live="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"></textarea>
                                @error('notes')
                                <label class="validation-error-label text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-gear-six"></i>
                        Hành động
                    </div>

                    <div class="card-body d-flex gap-2 flex-wrap">
                        <button wire:loading.remove wire:target="update" wire:click="update" class="btn btn-primary flex-grow-1 text-nowrap">
                            <i class="ph-floppy-disk"></i> Thêm
                        </button>

                        <button wire:loading wire:target="update" class="btn btn-primary flex-grow-1 text-nowrap" disabled>
                            <i class="ph-spinner-gap animate-spin"></i> Đang Thêm...
                        </button>

                        <a href="{{ route('equipment.index') }}" class="btn btn-warning flex-grow-1 text-nowrap">
                            <i class="ph-arrow-counter-clockwise"></i> Trở lại
                        </a>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <style>
        @media (max-width: 767px) {
            .page-header-content {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
        }
    </style>
</div>
