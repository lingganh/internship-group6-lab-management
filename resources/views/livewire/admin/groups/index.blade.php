<div>
    <div class="card">
        <div class="card-header py-3 d-flex justify-content-between">
            <div class="d-flex gap-2">
                <div>
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <a href="{{route('admin.groups.create')}}" type="button" class="btn btn-primary btn-icon px-2">
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
                    <th>TÊN NHÓM</th>
                    <th>TRƯỞNG NHÓM</th>
                    <th>SỐ THÀNH VIÊN</th>
                    <th>NGÀY TẠO</th>
                    <th>TRẠNG THÁI </th>
                    <th class="text-center">HÀNH ĐỘNG</th>
                </tr>
                </thead>
                <tbody>
                @forelse($groups as $group)
                    <tr>
                        <th>{{$loop->index+1 + $groups->perPage()* ($groups->currentPage()-1)}}</th>
                        <th>{{$group->name}}</th>
                        <th>{{$group->leader->full_name}}</th>
                        <th>{{$group->users()->count()}}</th>
                        <th>{{$group->created_at ? $group->created_at->format('d-m-Y') : ''}}</th>
                        <th>{!! $group->group_status !!}</th>
                        <td class="text-center">
                            <div class="dropdown ">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{route('admin.groups.edit', $group->id)}}" class="dropdown-item">
                                        <i class="ph-note-pencil px-1"></i>
                                        Chỉnh sửa
                                    </a>
                                    <a type="button" @click="$wire.openDeleteModal({{ $group->id }})" href="#" class="dropdown-item">
                                        <i class="ph-trash px-1"></i>
                                        Xóa
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                @empty
                    <x-table-empty :colspan="7"/>
                @endforelse
            </table>
        </div>
    </div>
    {{ $groups->links('vendor.pagination.theme') }}
</div>
