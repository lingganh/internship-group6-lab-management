<div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <h4 class="page-title mb-0">
                    Phòng Lab -
                    <span class="fw-normal">Thêm mới phòng lab</span>
                </h4>
            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="breadcrumb py-2">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                        <i class="ph-house"></i>
                    </a>
                    <a href="{{ route('admin.lab.index') }}" class="breadcrumb-item">
                        Danh sách phòng lab
                    </a>
                    <span class="breadcrumb-item active">Thêm mới</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="content">
        <div class="row g-3 align-items-start">

            {{-- FORM --}}
            <div class="col-xl-9 col-lg-8 col-12 order-1">
                <div class="card">
                    <div class="card-header fw-bold">
                        <i class="ph-info"></i> Thêm phòng lab mới
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6 col-12">
                                <label class="form-label">
                                    Tên phòng lab <span class="text-danger">*</span>
                                </label>
                                <input
                                    wire:model.live="name"
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">
                                    Mã phòng <span class="text-danger">*</span>
                                </label>
                                <input
                                    wire:model.lazy="code"
                                    type="text"
                                    class="form-control @error('code') is-invalid @enderror">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Vị trí</label>
                                <input
                                    wire:model.lazy="location"
                                    type="text"
                                    class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Mô tả</label>
                                <textarea
                                    rows="3"
                                    wire:model.lazy="description"
                                    class="form-control"></textarea>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">Trạng thái</label>
                                <select
                                    wire:model.lazy="status"
                                    class="form-select">
                                    <option value="active">Hoạt động</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="locked">Tạm khóa</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">Sức chứa</label>
                                <input
                                    wire:model.lazy="capacity"
                                    type="number"
                                    class="form-control @error('capacity') is-invalid @enderror">
                                @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ảnh minh họa</label>
                                <input
                                    wire:model="image"
                                    type="file"
                                    class="form-control">
                                @if ($image)
                                    <img
                                        src="{{ $image->temporaryUrl() }}"
                                        class="mt-2 rounded border"
                                        width="120">
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="col-xl-3 col-lg-4 col-12 order-2">
                <div class="card">
                    <div class="card-header fw-bold">
                        <i class="ph-gear-six"></i> Hành động
                    </div>

                    <div class="card-body d-flex align-items-center gap-2 flex-nowrap">
                        <button
                            wire:loading.remove
                            wire:target="save"
                            wire:click="save"
                            class="btn btn-primary text-nowrap"
                        >
                            <i class="ph-floppy-disk"></i> Thêm
                        </button>

                        <button
                            wire:loading
                            wire:target="save"
                            class="btn btn-primary text-nowrap"
                            disabled
                        >
                            <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                        </button>

                        <a
                            href="{{ route('admin.lab.index') }}"
                            class="btn btn-warning text-nowrap"
                        >
                            <i class="ph-arrow-counter-clockwise"></i> Trở lại
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
