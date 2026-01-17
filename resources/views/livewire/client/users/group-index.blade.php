<div class="row">
    <div class="col-lg-2 col-sm-0"></div>
    <div class="col-lg-8 col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <div class="d-flex gap-2">
                    <div>
                        <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        Số hàng mỗi trang:
                        <select wire:model.live="perPage" class="form-select d-inline-block w-auto" style="padding: 8px 24px 8px 10px;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
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
                        <th>SỐ THÀNH VIÊN</th>
                        <th>NGÀY TẠO</th>
                        <th class="text-center">HÀNH ĐỘNG</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($groups as $group)
                        <tr>
                            <td>{{$loop->index+1 + $groups->perPage()* ($groups->currentPage()-1)}}</td>
                            <td>{{$group->name}}</td>
                            <td>{{$group->students()->count()}}</td>
                            <td>{{$group->created_at ? $group->created_at->format('d-m-Y') : ''}}</td>
                            <td class="text-center">
                                <a wire:loading.remove wire:target="QuickView({{$group->id}})" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#quick-view-group" type="button" class="btn btn-warning"  @click="$wire.QuickView({{$group->id}})"><i class="ph ph-eye"></i> Xem chi tiết </a>
                                <a wire:loading wire:target="QuickView({{$group->id}})" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#quick-view-group" type="button" class="btn btn-warning"  @click="$wire.QuickView({{$group->id}})"><i class="ph-spinner-gap animate-spin"></i> Xem chi tiết </a>
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
    <div class="col-lg-2 col-sm-0"></div>
    <x-quick-view keyId="quick-view-group" title="Thông tin nhóm Nghiên cứu khoa học">
        <livewire:client.users.quickview-group />
    </x-quick-view>
</div>
