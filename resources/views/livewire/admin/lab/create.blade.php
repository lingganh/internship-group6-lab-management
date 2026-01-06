<div>
    <div>
        <x-slot name="header">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Phòng Lab - <span class="fw-normal">Danh sách Phòng lab </span>
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
                            <a href="{{route('admin.lab.index')}}" class="breadcrumb-item">Danh sách phòng lab </a>
                            <span class="breadcrumb-item active">Thêm mới phòng lab</span>
                        </div>

                        <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                            <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                        </a>
                    </div>

                </div>
            </div>
        </x-slot>

        <div class="content">
            <div class="row">
                <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header bold">
                            <i class="ph-info"></i>
                            Thêm phòng lab mới
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-6">
                                    <label for="name" class="col-form-label">
                                        Tên phòng lab: <span class="required">*</span>
                                    </label>
                                    <input wire:model.live="name" type="text" id="name" class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <label class="validation-error-label text-danger">{{ $message }}</label>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <label for="lab_id" class="col-form-label">
                                        Mã Phòng: <span class="required">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model.lazy="code"
                                           class="form-control @error('code') is-invalid @enderror">
                                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">Vị trí</label>
                                    <input type="text"
                                           wire:model.lazy="location"
                                           class="form-control @error('location') is-invalid @enderror">
                                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">Mô tả</label>
                                    <textarea rows="3"
                                              wire:model.lazy="description"
                                              class="form-control @error('description') is-invalid @enderror"></textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">Trạng thái</label>
                                    <select wire:model.lazy="status"
                                            class="form-select @error('status') is-invalid @enderror">
                                        <option value="active">Hoạt động</option>
                                        <option value="maintenance">Bảo trì</option>
                                        <option value="locked">Tạm khóa</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-6">
                                    <label class="form-label">Ảnh minh họa</label>

                                    <input type="file" wire:model="image" class="form-control">

                                    @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @if ($image)
                                        <div class="mt-2">
                                            <img src="{{ $image->temporaryUrl() }}" width="120">
                                        </div>
                                    @endif
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
                    <div class="card-body d-flex align-items-center gap-1">
                        <button wire:loading.remove wire:target="update" class="btn btn-primary" wire:click="update">
                            <i class="ph-floppy-disk"></i> Lưu
                        </button>
                        <button wire:loading wire:target="update" class="btn btn-primary" disabled>
                            <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                        </button>
                        <a href="{{ route('admin.lab.index') }}" class="btn btn-warning">
                            <i class="ph-arrow-counter-clockwise"></i> Trở lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
