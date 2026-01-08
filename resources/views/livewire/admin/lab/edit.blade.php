<div>
    <div>
        <x-slot name="header">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Phòng lab - <span class="fw-normal">Danh sách Phòng lab</span>
                        </h4>

                        <a href="#page_header"
                           class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                           data-bs-toggle="collapse">
                            <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                        </a>
                    </div>
                </div>

                <div class="page-header-content d-lg-flex border-top">
                    <div class="d-flex">
                        <div class="breadcrumb py-2">
                            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                                <i class="ph-house"></i>
                            </a>
                            <a href="{{ route('admin.lab.index') }}" class="breadcrumb-item">
                                Danh sách phòng lab
                            </a>
                            <span class="breadcrumb-item active">
                                Chỉnh sửa thông tin phòng lab
                            </span>
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

        <br>

        <div class="row">
            {{-- FORM --}}
            <div class="col-md-9 col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-info"></i>
                        Thông tin phòng lab
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Tên phòng <span class="text-danger">*</span>
                            </label>
                            <input
                                wire:model.lazy="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Mã phòng <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                wire:model="code"
                                readonly
                                style="background:#f5f5f5; cursor:not-allowed;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">Hoạt động</option>
                                <option value="maintenance">Bảo trì</option>
                                <option value="locked">Tạm khóa</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" wire:model="description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vị trí</label>
                            <input type="text" class="form-control" wire:model="location">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sức chứa</label>
                            <input
                                type="number"
                                wire:model.lazy="capacity"
                                class="form-control @error('capacity') is-invalid @enderror">
                            @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh minh họa</label>
                            <input type="file" wire:model="image" class="form-control">
                            @if($oldImage)
                                <img
                                    src="{{ asset('storage/'.$oldImage) }}"
                                    width="80"
                                    class="mt-2 rounded border">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="col-md-3 col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-gear-six"></i>
                        Hành động
                    </div>

                    {{-- 🔥 FIX RESPONSIVE: KHÔNG BAO GIỜ RỚT DÒNG --}}
                    <div class="card-body d-flex align-items-center gap-2 flex-nowrap">
                        <button
                            wire:loading.remove
                            wire:target="update"
                            wire:click="update"
                            class="btn btn-primary text-nowrap"
                        >
                            <i class="ph-floppy-disk"></i> Lưu
                        </button>

                        <button
                            wire:loading
                            wire:target="update"
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
