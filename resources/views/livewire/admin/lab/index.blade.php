<div>
    <div>
        <div>
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Danh sách Phòng lab
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
                            <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                            <span class="breadcrumb-item active">Danh sách phòng lab </span>
                        </div>

                        <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                            <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="card" style="margin: 2% 2% ">
            <div class="card-header py-3 d-flex justify-content-between">
                <div class="d-flex gap-2">
                    <div>
                        <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        Số hàng mỗi trang:
                        <select wire:model.live="perPage" class="form-select d-inline-block w-auto"
                                style="padding: 8px 24px 8px 10px;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div>
                        <a href="{{route('admin.lab.create')}}" type="button" class="btn btn-primary btn-icon px-2">
                            <i class="ph-plus-circle px-1"></i><span>Thêm mới</span>
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light btn-icon px-2" @click="$wire.$refresh">
                            <i class="ph-arrows-clockwise px-1"></i><span>Tải lại</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive-md">
                <table class="table fs-table text-center">
                    <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th>MÃ PHÒNG</th>
                        <th>TÊN PHÒNG </th>
                        <th> TRẠNG THÁI </th>
                        <th>MÔ TẢ </th>
                        <th>ẢNH MINH HỌA </th>
                        <th class="text-center">HÀNH ĐỘNG</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($labs as $index => $lab)
                        <tr>
                            <td>{{  $index +1 }}</td>
                            <td>{{ $lab->code }}</td>
                            <td>{{ $lab->name  }}</td>
                            <td>
                                @if($lab->status == 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($lab->status == 'maintenance')
                                    <span class="badge bg-warning">Bảo trì</span>
                                @else
                                    <span class="badge bg-danger">Tạm khóa</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($lab->description, 60) }}</td>
                            <td>
                                @if($lab->image_url)
                                    <img src="{{ asset('storage/'.$lab->image_url) }}" width="80">
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('admin.lab.edit', $lab->id) }}" class="dropdown-item">
                                            <i class="ph-note-pencil px-1"></i> Chỉnh sửa
                                        </a>
                                        <a type="button" @click="$wire.openDeleteModal({{ $lab->id }})" href="#" class="dropdown-item">
                                            <i class="ph-trash px-1"></i>
                                            Xóa
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="9" />
                    @endforelse


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


