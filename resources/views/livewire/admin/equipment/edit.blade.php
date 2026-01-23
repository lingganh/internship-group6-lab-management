<div>
    <div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow-sm">
            <div class="page-header-content d-flex align-items-center justify-content-between py-2 px-3">
                <div class="breadcrumb mb-0">
                    <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house me-2"></i></a>
                    <span class="breadcrumb-item">Danh sách thiết bị</span>
                    <span class="breadcrumb-item active fw-semibold text-primary">{{ $name }} - {{ $code }}</span>
                    <span class="breadcrumb-item active fw-semibold text-warning">Chỉnh sửa</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-lg-9 col-12 mb-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-transparent d-flex align-items-center border-bottom py-3">
                        <i class="ph-info me-2 text-primary fs-5"></i>
                        <h6 class="mb-0 fw-bold">Thông tin thiết bị</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên thiết bị <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ph-textbox text-muted"></i></span>
                                    <input wire:model.blur="name" type="text" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" placeholder="Nhập tên thiết bị">
                                </div>
                                @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mã thiết bị <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ph-barcode text-muted"></i></span>
                                    <input wire:model.blur="code" type="text" class="form-control bg-light border-start-0 @error('code') is-invalid @enderror" placeholder="VD: EQ-275-YC">
                                </div>
                                @error('code') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Loại thiết bị <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ph-tag text-muted"></i></span>
                                    <input wire:model.blur="type" type="text" class="form-control bg-light border-start-0 @error('type') is-invalid @enderror">
                                </div>
                                @error('type') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phòng Lab <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ph-buildings text-muted"></i></span>
                                    <select wire:model.live="lab_id" class="form-select bg-light border-start-0 @error('lab_id') is-invalid @enderror">
                                        <option value="">-- Chọn phòng lab --</option>
                                        @foreach ($labs as $lab)
                                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('lab_id') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Ghi chú</label>
                                <textarea wire:model.blur="notes" rows="3" class="form-control bg-light" placeholder="Nhập ghi chú hoặc mô tả..."></textarea>
                            </div>
                        </div>

                        <div class="row g-3 mt-4 p-3 rounded-3 border mx-0" style="background-color: #fcfcfc;">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Số lượng tổng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="ph-stack text-muted"></i></span>
                                    <input wire:model.live.debounce.500ms="quantity" type="number" class="form-control @error('quantity') is-invalid @enderror">
                                </div>
                                @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-danger">Số lượng hỏng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-danger border-danger"><i class="ph-warning-circle"></i></span>
                                    <input wire:model.live.debounce.500ms="broken_quantity" type="number" class="form-control border-danger text-danger @error('broken_quantity') is-invalid @enderror">
                                </div>
                                @error('broken_quantity') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-success">Số lượng thực sử dụng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white border-success"><i class="ph-check-circle"></i></span>
                                    <input type="number" value="{{ $actual_quantity }}" class="form-control fw-bold border-success text-success bg-light" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-0 shadow-sm mt-3">
                    @include('pages.client.equipment.issues.issues-block', [
                        'equipmentId' => $equipmentId,
                        'issues' => $issues,
                        'labItems' => $labItems,
                    ])
                </div>
            </div>

  <div class="col-md-3 col-12">
    <div class="card shadow-sm">
        <div class="card-header bold">
            <i class="ph-gear-six"></i>
            Hành động
        </div>

        <div class="card-body">
            {{-- <div class="mb-3">
                <label class="form-label">Trạng thái thiết bị</label>
                <select wire:model="status" class="form-select">
                    <option value="available">Sẵn sàng</option>
                    <option value="in_use">Đang sử dụng</option>
                    <option value="maintenance">Bảo trì</option>
                    <option value="broken">Hỏng</option>
                </select>
            </div> --}}

            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <button wire:loading.remove wire:target="update" wire:click="update" class="btn btn-primary text-nowrap">
                    <i class="ph-floppy-disk"></i> Lưu
                </button>

                <button wire:loading wire:target="update" class="btn btn-primary text-nowrap" disabled>
                    <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                </button>

                <a href="{{ route('equipment.index') }}" class="btn btn-warning text-nowrap text-white">
                    <i class="ph-arrow-counter-clockwise"></i> Trở lại
                </a>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
    
</div>  
<style>
     .input-group:has(.is-invalid) {
        border: 1px solid #d93025 !important;
        border-radius: 0.375rem;
    }

     .input-group:has(.is-invalid) .input-group-text,
    .input-group:has(.is-invalid) input,
    .input-group:has(.is-invalid) select {
        border-color: transparent !important;
    }

     .input-group:has(.is-invalid) .input-group-text i {
        color: #d93025 !important;
    }
</style>
</div>