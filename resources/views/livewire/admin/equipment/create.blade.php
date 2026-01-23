<div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex flex-column flex-md-row gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <h4 class="page-title mb-0">
                        Thiết bị -
                        <span class="fw-normal">Thêm thiết bị vào phòng</span>
                    </h4>

                    <a href="#page_header"
                       class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                       data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>

            <div class="page-header-content d-lg-flex flex-column flex-md-row gap-2 border-top">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="breadcrumb py-2">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <a href="{{ route('equipment.index') }}" class="breadcrumb-item">Danh sách thiết bị</a>
                        <span class="breadcrumb-item active">Thêm vào phòng</span>
                    </div>

                    <a href="#breadcrumb_elements"
                       class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                       data-bs-toggle="collapse">
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
                        Thông tin
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            {{-- MODE --}}
                            <div class="col-12">
                                <div class="d-flex gap-3 flex-wrap">
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" wire:model.live="mode" value="existing">
                                        <span class="form-check-label">Thiết bị đã có</span>
                                    </label>

                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" wire:model.live="mode" value="new">
                                        <span class="form-check-label">Thiết bị mới</span>
                                    </label>
                                </div>
                                @error('mode') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            {{-- EXISTING --}}
                            @if($mode === 'existing')
                                <div class="col-md-12 col-12">
                                    <label class="col-form-label">
                                        Chọn thiết bị <span class="required">*</span>
                                    </label>
                                    <select wire:model.live="equipment_id"
                                            class="form-control @error('equipment_id') is-invalid @enderror">
                                        <option value="">Chọn thiết bị...</option>
                                        @foreach($equipments as $eq)
                                            <option value="{{ $eq->id }}">
                                                {{ $eq->name }} ({{ $eq->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            {{-- NEW --}}
                            @if($mode === 'new')
                                <div class="col-md-6 col-12">
                                    <label class="col-form-label">Tên thiết bị <span class="required">*</span></label>
                                    <input wire:model.live="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="col-form-label">Mã thiết bị <span class="required">*</span></label>
                                    <input wire:model.live="code" type="text"
                                           class="form-control @error('code') is-invalid @enderror">
                                    @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="col-form-label">Loại thiết bị <span class="required">*</span></label>
                                    <input wire:model.live="type" type="text"
                                           class="form-control @error('type') is-invalid @enderror">
                                    @error('type') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="col-form-label">Trạng thái</label>
                                    <select wire:model.live="status"
                                            class="form-control @error('status') is-invalid @enderror">
                                        <option value="available">Sẵn sàng sử dụng</option>
                                        <option value="in_use">Đang sử dụng</option>
                                        <option value="maintenance">Bảo trì</option>
                                        <option value="broken">Hỏng</option>
                                    </select>
                                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="col-form-label">Ghi chú</label>
                                    <textarea wire:model.live="notes" rows="3"
                                              class="form-control @error('notes') is-invalid @enderror"></textarea>
                                    @error('notes') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            <hr class="my-2">

                            {{-- PIVOT --}}
                            <div class="col-md-6 col-12">
                                <label class="col-form-label">Phòng LAB <span class="required">*</span></label>
                                <select wire:model.live="lab_id"
                                        class="form-control @error('lab_id') is-invalid @enderror">
                                    <option value="">Chọn phòng...</option>
                                    @foreach($labs as $lab)
                                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                    @endforeach
                                </select>
                                @error('lab_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="col-form-label">Số lượng <span class="required">*</span></label>
                                <input wire:model.live="quantity" type="number" min="0"
                                       class="form-control @error('quantity') is-invalid @enderror">
                                @error('quantity') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="col-form-label">Số lượng hỏng <span class="required">*</span></label>
                                <input wire:model.live="broken_quantity" type="number" min="0"
                                       class="form-control @error('broken_quantity') is-invalid @enderror">
                                @error('broken_quantity') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="col-form-label">Số lượng thực</label>
                                <input type="number" class="form-control" value="{{ $actual_quantity }}" disabled>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="col-md-3 col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-gear-six"></i> Hành động
                    </div>

                    <div class="card-body d-flex gap-2 flex-wrap">
                        <button wire:loading.remove wire:target="save" wire:click="save"
                                class="btn btn-primary flex-grow-1">
                            <i class="ph-floppy-disk"></i> Lưu
                        </button>

                        <button wire:loading wire:target="save" class="btn btn-primary flex-grow-1" disabled>
                            <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                        </button>

                        <a href="{{ route('equipment.index') }}" class="btn btn-warning flex-grow-1">
                            <i class="ph-arrow-counter-clockwise"></i> Trở lại
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media (max-width: 767px) {
            .page-header-content { flex-direction: column !important; gap: .5rem !important; }
        }
    </style>
</div>
