<div>
    <div class="card">
        <div class="card-header py-3 d-flex justify-content-between">
            <div class="d-flex gap-2">
                <div>
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
                <div wire:ignore>
                    <select id="status-select" class="form-control multiselect" multiple="multiple">
                        @foreach($userStatus as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{route('admin.users.create')}}" type="button" class="btn btn-primary btn-icon px-2">
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
            <table class="table fs-table ">
                <thead>
                <tr class="table-light">
                    <th>STT</th>
                    <th>TÊN TÀI KHOẢN</th>
                    <th>MÃ SV/GV</th>
                    <th>EMAIL</th>
                    <th>VAI TRÒ</th>
                    <th>TRẠNG THÁI</th>
                    <th class="text-center">HÀNH ĐỘNG</th>
                </tr>
                </thead>
{{--                @dd($users)--}}
                <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{$loop->index+1 +$users->perPage() * ($users->currentPage()-1)}}</td>
                    <td>{{$user->full_name}}</td>
                    <td>{{$user->code}}</td>
                    <td>{{$user->email!=null?$user->email:''}}</td>
{{--                    <td>{{$user->role->name}}</td>--}}
                    <td>{!! $user->role_text !!}</td>
{{--                    <td>@if($user->email_verified_at===null && $user->sso_id === null) Chưa xác minh email @elseif($user->email_verified_at===null && $user->sso_id !== null) Chưa thiết lập mật khẩu @elseif($user->email_verified_at!==null && $user->sso_id === null) Chưa thiết lập SSO @else Bình thường  @endif</td>--}}
                    <td>{!! $user->user_status !!}</td>
                    <td class="text-center">
                        <div class="dropdown ">
                            <a href="#" class="text-body" data-bs-toggle="dropdown">
                                <i class="ph-list"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($user->status===\App\Enums\UserStatus::Archived->value)
                                    <a type="button" @click="$wire.openRearchiveModal({{ $user->id }})" href="#" class="dropdown-item">
                                        <i class="ph-arrow-counter-clockwise px-1"></i>
                                        Khôi phục tài khoản
                                    </a>
                                    <a href="{{route('admin.users.edit', $user->id)}}" class="dropdown-item">
                                        <i class="ph-note-pencil px-1"></i>
                                        Chỉnh sửa
                                    </a>
                                @else
                                    @if($user->status===\App\Enums\UserStatus::Pending->value)
                                        <a type="button" @click="$wire.openApproveModal({{ $user->id }})" href="#" class="dropdown-item">
                                            <i class="ph-checks px-1"></i>
                                            Duyệt tài khoản
                                        </a>
                                    @endif
                                    <a href="{{route('admin.users.edit', $user->id)}}" class="dropdown-item">
                                        <i class="ph-note-pencil px-1"></i>
                                        Chỉnh sửa
                                    </a>
                                    @if($user->status===\App\Enums\UserStatus::Approved->value && $user->id !== auth()->id())
                                        <a type="button" @click="$wire.openArchiveModal({{ $user->id }})" href="#" class="dropdown-item">
                                            <i class="ph-archive px-1"></i>
                                            Lưu trữ tài khoản
                                        </a>
                                    @endif
                                    @if($user->email_verified_at===null && $user->sso_id === null)
                                        <a type="button" @click="$wire.openDeleteModal({{ $user->id }})" href="#" class="dropdown-item">
                                            <i class="ph-trash px-1"></i>
                                            Xóa
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty

                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $users->links('vendor.pagination.theme') }}
    @section('script_custom')
        <script src="{{asset('assets/js/vendor/forms/selects/bootstrap_multiselect.js')}}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('#status-select').multiselect({
                    // Cấu hình cơ bản
                    buttonWidth: '200px',
                    nonSelectedText: 'Chọn trạng thái',
                    allSelectedText: 'Đã chọn tất cả',
                    numberDisplayed: 2,

                    onChange: function(option, checked) {
                        var data = $('#status-select').val();
                    @this.set('status', data);
                    }
                });
            });
        </script>
    @endsection
</div>
