<div class="row">
    <div class="col-md-9 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-info"></i>
                Thông tin người dùng
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="name" class="col-form-label" wire:ignore>
                            Tên nhóm: <span class="required">*</span>
                        </label>
                        <input wire:model.live="name" type="text" id="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                        <label id="error-name" class="validation-error-label text-danger"
                               for="name">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="leaderId" class="col-form-label">
                            Trưởng nhóm: <span class="required">*</span>
                        </label>
                        <select id="selectLeader" class="form-control select @error('leaderId') is-invalid @enderror" wire:model.live="leaderId">
                            <option value="0">Chọn trưởng nhóm ...</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{ $user->full_name .' - '.  $user->code }}</option>
                            @endforeach
                        </select>
                        @error('leaderId')
                        <label id="error-leaderId" class="validation-error-label text-danger"
                               for="leaderId">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label for="description" class="col-form-label @error('description') is-invalid @enderror" wire:ignore>
                            Mô tả: <span class="required"></span>
                        </label>
                        <textarea wire:model.live="description" id="description" class="form-control" rows="4"  placeholder="">

                        </textarea>
                        @error('description')
                        <label id="error-description" class="validation-error-label text-danger"
                               for="description">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-9">
                        <label for="leaderId" class="col-form-label">
                            Thêm thành viên: <span class="required">*</span>
                        </label>
                        <select id="selectLeader" class="form-control select @error('groupMemberId') is-invalid @enderror" @if($leaderId===0) disabled @endif wire:model.live="groupMemberId">
                            <option value="0">Chọn thành viên ...</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" class="@if($user->id === $leaderId) d-none @endif @if(in_array($user, $groupMembers)) d-none @endif">{{ $user->full_name .' - '.  $user->code }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <label id="error-leaderId" class="validation-error-label text-danger"
                               for="leaderId">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-3 d-flex align-items-end">
                        <button class="btn btn-success @if($groupMemberId==0) disabled @endif" @click="$wire.addUser({{$groupMemberId}})">
                            <span wire:loading.remove wire:target="addUser"><i class="ph-plus-circle"></i> Thêm</span>
                            <span wire:loading wire:target="addUser"><i class="ph-spinner-gap animate-spin"></i> Đang lưu...</span>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label for="leaderId" class="col-form-label">
                            Danh sách thàn viên nhóm: <span class="required">*</span>
                        </label>
                        <div class="table-responsive-md" style="position: relative">
                            <table class="table fs-table text-center">
                                <thead>
                                <tr class="table-light">
                                    <th>STT</th>
                                    <th>HỌ VÀ TÊN</th>
                                    <th>Mã sinh viên</th>
                                    <th>Chức vụ</th>
                                    <th class="text-center">HÀNH ĐỘNG</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($groupMembers as $groupMember)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$groupMember->full_name}}</td>
                                        <td>{{$groupMember->code}}</td>
                                        <td>@if($groupMember->id === $leaderId) Trưởng nhóm @else Thành viên @endif</td>
                                        <td>
                                            <a wire:loading.remove wire:target="QuickView({{$groupMember->id}})" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#quick-view" type="button" class="btn btn-warning"  @click="$wire.QuickView({{$groupMember->id}})"><i class="ph-eye"></i> Xem </a>
                                            <a wire:loading wire:target="QuickView({{$groupMember->id}})" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#quick-view" type="button" class="btn btn-warning"  @click="$wire.QuickView({{$groupMember->id}})"><i class="ph-spinner-gap animate-spin"></i> Xem </a>
                                            <button type="button" class="btn btn-danger @if($groupMember->id === $leaderId) disabled @endif" @click="$wire.removeUser({{ $groupMember->id }})">
                                                <span wire:loading.remove wire:target="removeUser({{ $groupMember->id }})"><i class="ph-trash"></i> Xóa</span>
                                                <span wire:loading wire:target="removeUser({{ $groupMember->id }})"><i class="ph-spinner-gap animate-spin"></i> Đang xóa ...</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                @empty
                                    <x-table-empty :colspan="7"/>
                                @endforelse
                            </table>
                            <div class="spinner-overlay" wire:loading wire:target="addUser, removeUser" wire:loading.class="d-flex">
                                <div class="spinner-border text-primary" role="status"></div>
                                <span class="">Đang xử lý...</span>
                            </div>
                        </div>
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
                <button wire:loading.remove wire:target="save" class="btn btn-primary" @click="$wire.save"><i class="ph-floppy-disk"></i> Lưu</button>
                <button wire:loading wire:target="save" class="btn btn-primary" @click="$wire.save"><i class="ph-spinner-gap animate-spin"></i> Lưu</button>
                <a href="{{route('admin.groups.index')}}" type="button" class="btn btn-warning"><i class="ph-arrow-counter-clockwise"></i> Trở lại</a>
            </div>
        </div>
    </div>
    <x-quick-view keyId="quick-view" title="Thông tin người dùng">
        <livewire:admin.groups.quick-view :userId="$quickViewUserId" :key="$quickViewUserId"/>
    </x-quick-view>
</div>
