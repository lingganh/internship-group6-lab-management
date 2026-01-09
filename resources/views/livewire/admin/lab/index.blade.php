<div>
    <div class="page-header page-header-light shadow mb-3">
        <div class="page-header-content d-flex flex-wrap align-items-center gap-2">
            <h4 class="page-title mb-0">
                Danh sách Phòng lab
            </h4>
        </div>

        <div class="page-header-content border-top">
            <div class="breadcrumb py-2">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                    <i class="ph-house"></i>
                </a>
                <span class="breadcrumb-item active">Danh sách phòng lab</span>
            </div>
        </div>
    </div>

    <div class="card m-2">
        <div class="card-header py-3 d-flex flex-wrap gap-2 justify-content-between">
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <input wire:model.live="search" type="text" class="form-control" style="max-width: 260px" placeholder="Tìm kiếm...">
            </div>
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <div class="d-flex align-items-center gap-1">
                    <span class="small">Số hàng:</span>
                    <select wire:model.live="perPage" class="form-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <a href="{{ route('admin.lab.create') }}" class="btn btn-primary btn-icon">
                    <i class="ph-plus-circle"></i>
                    <span class="d-none d-md-inline">Thêm mới</span>
                </a>

                <button type="button" class="btn btn-light btn-icon" @click="$wire.$refresh">
                    <i class="ph-arrows-clockwise"></i>
                    <span class="d-none d-md-inline">Tải lại</span>
                </button>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table text-center align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Mã</th>
                    <th>Tên phòng</th>
                    <th>Trạng thái</th>
                    <th class="d-none d-md-table-cell">Mô tả</th>
                    <th>Ảnh</th>
                    <th>Hành động</th>
                </tr>
                </thead>

                <tbody>
                @forelse($labs as $index => $lab)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $lab->code }}</td>
                        <td>{{ $lab->name }}</td>
                        <td>
                            @if($lab->status == 'active')
                                <span class="badge bg-success">Hoạt động</span>
                            @elseif($lab->status == 'maintenance')
                                <span class="badge bg-warning">Bảo trì</span>
                            @else
                                <span class="badge bg-danger">Tạm khóa</span>
                            @endif
                        </td>

                        <td class="d-none d-md-table-cell text-start">
                            {{ Str::limit($lab->description, 60) }}
                        </td>

                        <td>
                            @if($lab->image_url)
                                <img
                                    src="{{ asset('storage/'.$lab->image_url) }}"
                                    class="img-fluid rounded"
                                    style="max-width: 70px"
                                >
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.lab.edit', $lab->id) }}" class="dropdown-item">
                                        <i class="ph-note-pencil me-1"></i> Chỉnh sửa
                                    </a>

                                    <button type="button"
                                            class="dropdown-item"
                                            @click="$wire.openDeleteModal({{ $lab->id }})">
                                        <i class="ph-trash me-1"></i> Xóa
                                    </button>

                                    @if($lab->guide_file)
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ Storage::url($lab->guide_file) }}"
                                           target="_blank"
                                           class="dropdown-item">
                                            <i class="ph-file-pdf me-1"></i> Hướng dẫn
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table-empty :colspan="7"/>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
