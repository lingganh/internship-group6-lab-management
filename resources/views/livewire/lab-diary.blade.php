<div>
    <div>
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Nhật ký sử dụng 
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
                        <span class="breadcrumb-item active">Nhật ký sử dụng </span>
                    </div>

                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <br><br><br>
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
                        <th> GIÁO VIÊN HƯỚNG DẪN </th>
                        <th>SỐ THÀNH VIÊN</th>
                        <th>NGÀY TẠO</th>
                        <th>TRẠNG THÁI </th>
                        <th class="text-center">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <td class="text-center">
                            <div class="dropdown ">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="" class="dropdown-item">
                                        <i class="ph-note-pencil px-1"></i>
                                        Chỉnh sửa
                                    </a>
                                    <a type="button" href="#" class="dropdown-item">
                                        <i class="ph-trash px-1"></i>
                                        Xóa
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <x-table-empty :colspan="7" />
            </table>
        </div>
    </div>
</div>
</div>