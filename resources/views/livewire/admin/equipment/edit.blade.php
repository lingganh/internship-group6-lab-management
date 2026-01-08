<div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-flex flex-column flex-lg-row justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-lg-0">
                    <h4 class="page-title mb-0">
                        Thiết bị - <span class="fw-normal">Danh sách thiết bị</span>
                    </h4>

                    <a href="#page_header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>

            <div class="page-header-content d-flex flex-column flex-lg-row justify-content-between border-top">
                <div class="d-flex align-items-center mb-2 mb-lg-0">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <a href="{{route('equipment.index')}}" class="breadcrumb-item">Danh sách thiết bị</a>
                        <span class="breadcrumb-item active">Chỉnh sửa thiết bị</span>
                    </div>

                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <br>


    <div class="row">
        <div class="col-md-9 col-12 mb-3 mb-md-0">
            <div class="card">
                <div class="card-header bold">
                    <i class="ph-info"></i>
                    Thông tin thiết bị
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thiết bị <span class="text-danger">*</span></label>
                        <input wire:model.lazy="name" type="text" id="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Mã thiết bị <span class="text-danger">*</span></label>
                        <input wire:model.lazy="code" type="text" id="code" class="form-control @error('code') is-invalid @enderror">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Loại thiết bị <span class="text-danger">*</span></label>
                        <input wire:model.lazy="type" type="text" id="type" class="form-control @error('type') is-invalid @enderror">
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lab_id" class="form-label">Phòng Lab <span class="text-danger">*</span></label>
                        <select wire:model.lazy="lab_id" id="lab_id" class="form-select @error('lab_id') is-invalid @enderror">
                            <option value="">-- Chọn phòng lab --</option>
                            @foreach($labs as $lab)
                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                            @endforeach
                        </select>
                        @error('lab_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="purchased_date" class="form-label">Ngày sửa</label>
                        <input wire:model.lazy="purchased_date" type="date" id="purchased_date" class="form-control @error('purchased_date') is-invalid @enderror">
                        @error('purchased_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="specifications" class="form-label">Thông số kỹ thuật</label>
                        <textarea wire:model.lazy="specifications" id="specifications" rows="3" class="form-control @error('specifications') is-invalid @enderror"></textarea>
                        @error('specifications') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea wire:model.lazy="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"></textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng tổng <span class="text-danger">*</span></label>
                        <input wire:model.lazy="quantity" type="number" min="0" id="quantity" class="form-control @error('quantity') is-invalid @enderror">
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="broken_quantity" class="form-label">Số lượng hỏng <span class="text-danger">*</span></label>
                        <input wire:model.lazy="broken_quantity" type="number" min="0" max="{{ $quantity }}" id="broken_quantity" class="form-control @error('broken_quantity') is-invalid @enderror">
                        @error('broken_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="actual_quantity" class="form-label">Số lượng thực</label>
                        <input wire:model="actual_quantity" type="number" id="actual_quantity" readonly class="form-control">
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


                <div class="card-body d-flex align-items-center gap-2 flex-nowrap">
                    <button wire:loading.remove wire:target="update" wire:click="update" class="btn btn-primary text-nowrap">
                        <i class="ph-floppy-disk"></i> Lưu
                    </button>

                    <button wire:loading wire:target="update" class="btn btn-primary text-nowrap" disabled>
                        <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                    </button>

                    <a href="{{ route('equipment.index') }}" class="btn btn-warning text-nowrap">
                        <i class="ph-arrow-counter-clockwise"></i> Trở lại
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
