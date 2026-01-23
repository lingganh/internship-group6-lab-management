<div>
    <div>
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Danh sách thiết bị
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
                        <span class="breadcrumb-item active">Danh sách thiết bị </span>
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
                    <a href="{{route('admin.equipment.create')}}" type="button" class="btn btn-primary btn-icon px-2">
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
                    <th>TÊN THIẾT BỊ </th>
                    <th>PHÒNG LAB</th>
                    <th> SỐ LƯỢNG </th>
                    <th>SỐ LƯỢNG HỎNG </th>
                    <th>SỐ LƯỢNG THỰC </th>
                   {{-- <th>TRẠNG THÁI </th>--}}
                    <th>MÔ TẢ </th>
                    <th class="text-center">HÀNH ĐỘNG</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td>{{ $item->equipment->name ?? '—' }}</td>
                        <td>{{ $item->lab->name ?? '—' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->broken_quantity }}</td>
                        <td>{{ $item->actual_quantity }}</td>
                      {{--  <td>
                            @php $status = $item->equipment->status ?? 'unknown'; @endphp
                            @switch($status)
                                @case('available')
                                    <span class="badge bg-success">Sẵn sàng sử dụng</span>
                                    @break
                                @case('in_use')
                                    <span class="badge bg-primary">Đang sử dụng</span>
                                    @break
                                @case('maintenance')
                                    <span class="badge bg-warning text-dark">Bảo trì</span>
                                    @break
                                @case('broken')
                                    <span class="badge bg-danger">Hỏng</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Không xác định</span>
                            @endswitch
                        </td>--}}
                        <td title="{{ $item->equipment->notes ?? '—' }}">
                            {{ \Illuminate\Support\Str::limit($item->equipment->notes ?? '—', 20, '...') }}
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.equipment.edit', $item->equipment->id) }}" class="dropdown-item">
                                        <i class="ph-note-pencil px-1"></i> Chỉnh sửa
                                    </a>
                                    <a href="#"
                                       wire:click.prevent="openDeleteModal({{ $item->equipment->id }})" class="dropdown-item">
                                        <i class="ph-trash px-1"></i> Xóa
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
