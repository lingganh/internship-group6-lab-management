<div x-data="approvalComponent()">
    <div class="page-header page-header-light shadow-sm">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">Danh sách lịch</h4>
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
                    <span class="breadcrumb-item active">Danh sách lịch</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4 approval-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-11">
                <div class="card border-0 approval-card">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <h4 class="mb-1 fw-semibold text-dark">Danh sách lịch</h4>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge approval-badge-warn">
                                    <span class="me-1">Đang chờ</span>
                                    <span class="fw-bold">{{ $pendingCount }}</span>
                                </span>
                               
                                @if(count($selectedIds) > 0)
                                    <button class="btn btn-success"
                                            wire:click="approveSelected"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="approveSelected">
                                            Phê duyệt {{ count($selectedIds) }} lịch
                                        </span>
                                        <span wire:loading wire:target="approveSelected">
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            Đang xử lý...
                                        </span>
                                    </button>
                                     
                                    <button class="btn btn-danger"
                                            wire:click="rejectSelected"
                                            wire:loading.attr="disabled"
                                            wire:target="rejectSelected,rejectScheduleBatch,performConfirm">
                                        <span wire:loading.remove wire:target="rejectSelected,rejectScheduleBatch,performConfirm">
                                            <i class="ph-x-circle me-1"></i>
                                            Từ chối {{ count($selectedIds) }} lịch
                                        </span>
                                        <span wire:loading wire:target="rejectSelected,rejectScheduleBatch,performConfirm">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Đang từ chối...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body pt-3">
                        <div class="approval-filters mb-3">
                            <div class="row g-2 g-md-3 align-items-end">
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                                    <select wire:model.live="filterStatus" class="form-select approval-control">
                                        <option value="pending">Chờ phê duyệt</option>
                                        <option value="approved">Đã phê duyệt</option>
                                        <option value="cancelled">Đã từ chối / hủy</option>
                                        <option value="completed">Đã hoàn thành</option>
                                        <option value="">Tất cả</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">Người dùng</label>
                                    <select wire:model.live="filterUserId" class="form-select approval-control">
                                        <option value="">Tất cả người dùng</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">Ngày</label>
                                    <input type="date"
                                           wire:model.live="filterDate"
                                           class="form-control approval-control">
                                </div>
                            </div>
                        </div>

                        <div class="approval-table-wrap">
                            <table class="table align-middle mb-0 approval-table">
                                <thead>
                                    <tr>
                                        <th>
                                            @if($pendingCount > 0)
                                            <input type="checkbox" 
                                                   wire:click="toggleSelectAll"
                                                   @if(count($selectedIds) > 0) checked @endif>
                                            @endif
                                        </th>
                                        <th>Sự kiện</th>
                                        <th>Mã phòng</th>
                                        <th>Người đăng ký</th>
                                        <th>Thời gian</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $item)
                                        <tr wire:key="schedule-{{ $item->id }}">
                                            <td>
                                                @if($item->status === 'pending')
                                                    <input type="checkbox"
                                                           wire:model.live="selectedIds"
                                                           value="{{ $item->id }}">
                                                @endif
                                            </td>

                                            <td data-label="Sự kiện">
                                                <div class="fw-semibold text-dark text-wrap-mobile d-flex align-items-center gap-1">
                                                    {{ $item->title }}
                                                    {{-- @if($item->series_id)
                                                        <span class="badge bg-info text-white" title="Lịch lặp"><i class="fa-solid fa-repeat"></i></span>
                                                    @endif --}}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $this->categoryLabel($item->category) }}
                                                    @if($item->series_id)
                                                        <span class="ms-1">(lịch lặp)</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td data-label="Mã phòng">
                                                <div class="fw-semibold text-dark">{{ $item->lab_code ?? '' }}</div>
                                                <div class="small text-muted">{{ $item->lab?->name }}</div>
                                            </td>

                                            <td data-label="Người đăng ký">
                                                <div class="fw-semibold text-dark">{{ $item->user?->full_name ?? '' }}</div>
                                                <div class="small text-muted text-break">ID: {{ $item->user_id }}</div>
                                            </td>

                                            <td data-label="Thời gian">
                                                <div class="fw-semibold text-dark">{{ optional($item->start)->format('d/m/Y') }}</div>
                                                <div class="small text-muted">
                                                    {{ optional($item->start)->format('H:i') }} – {{ optional($item->end)->format('H:i') }}
                                                </div>
                                            </td>

                                            <td data-label="Trạng thái" class="text-center-desktop">
                                                @if($item->status === 'pending')
                                                    <span class="badge approval-pill approval-pill-pending">Chờ duyệt</span>
                                                @elseif($item->status === 'approved')
                                                    <span class="badge approval-pill approval-pill-approved">Đã duyệt</span>
                                                @elseif($item->status === 'completed')
                                                    <span class="badge approval-pill approval-pill-approved">Đã hoàn thành</span>
                                                @else
                                                    <span class="badge approval-pill approval-pill-cancelled">Từ chối / hủy </span>
                                                @endif
                                            </td>

                                            <td class="text-center action-cell" data-label="Hành động">
                                                <div class="dropdown approval-action-dropdown">
                                                    <button class="btn btn-sm btn-link approval-action-toggle"
                                                            type="button"
                                                            data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        <i class="ph-dots-three-outline"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li>
                                                            <button type="button" class="dropdown-item"
                                                                    wire:click="viewSchedule({{ $item->id }})">
                                                                Chi tiết
                                                            </button>
                                                        </li>
                                                        @if($item->status === 'pending')
                                                            <li>
                                                                <button type="button" class="dropdown-item"
                                                                        wire:click="approveNow({{ $item->id }})">
                                                                    Phê duyệt
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item"
                                                                        wire:click="confirmReject({{ $item->id }})">
                                                                    Từ chối
                                                                </button>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger"
                                                                    wire:click="confirmDelete({{ $item->id }})">
                                                                Xóa lịch
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">Không có dữ liệu phù hợp.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex justify-content-center">
                            {{ $schedules->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Details --}}
            <div x-show="$wire.selectedSchedule !== null"
                 x-cloak
                 @open-details-modal.window="showModal('details')"
                 @close-details-modal.window="hideModal('details')"
                 class="modal fade"
                 :class="{ 'show d-block': modals.details }"
                 tabindex="-1"
                 style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-semibold text-dark mb-1">Chi tiết lịch đăng ký</h5>
                                <div class="small text-muted">Xem và chỉnh sửa thông tin lịch sử dụng phòng lab.</div>
                            </div>
                            <button type="button" class="btn-close mt-1" @click="hideModal('details')"></button>
                        </div>

                        <div class="modal-body pt-3">
                            @if($selectedSchedule)
                                <div class="row g-3">
                                    <div class="col-12 col-md-8">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                                            <input wire:model.defer="edit.title" type="text" class="form-control approval-control">
                                            @error('edit.title') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Phân loại</label>
                                            <select wire:model.defer="edit.category" class="form-select approval-control">
                                                <option value="work">Làm việc / nghiên cứu</option>
                                                <option value="seminar">Hội thảo / seminar</option>
                                                <option value="other">Khác</option>
                                            </select>
                                            @error('edit.category') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                            <select wire:model.defer="edit.lab_code" class="form-select approval-control">
                                                <option value="">Chọn phòng...</option>
                                                @foreach($labs as $lab)
                                                    <option wire:key="lab-edit-{{ $lab->code }}" value="{{ $lab->code }}">
                                                        {{ $lab->name }} ({{ $lab->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('edit.lab_code') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Bắt đầu</label>
                                            <input wire:model.defer="edit.start" type="datetime-local" class="form-control approval-control">
                                            @error('edit.start') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Kết thúc</label>
                                            <input wire:model.defer="edit.end" type="datetime-local" class="form-control approval-control">
                                            @error('edit.end') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                                            <select wire:model.defer="edit.status" class="form-select approval-control">
                                                <option value="pending">Chờ duyệt</option>
                                                <option value="approved">Đã duyệt</option>
                                                <option value="cancelled">Từ chối</option>
                                                <option value="completed">Đã hoàn thành</option>
                                            </select>
                                            @error('edit.status') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Đăng ký bởi</label>
                                            <select wire:model.defer="edit.user_id" class="form-select approval-control">
                                                <option value="">Chọn người dùng...</option>
                                                @foreach($users as $u)
                                                    <option wire:key="user-{{ $u->id }}" value="{{ $u->id }}">
                                                        {{ $u->full_name ?? $u->name ?? 'User #'.$u->id }}{{ $u->email ? ' ('.$u->email.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('edit.user_id') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Đăng ký cho nhóm</label>
                                            <select wire:model.defer="edit.group_id" class="form-select approval-control">
                                                <option value="">Chọn nhóm / lớp...</option>
                                                @foreach($groups as $g)
                                                    <option wire:key="group-{{ $g->id }}" value="{{ $g->id }}">
                                                        {{ $g->name ?? ('Group #'.$g->id) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('edit.group_id') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                            <textarea wire:model.defer="edit.description"
                                                      class="form-control approval-control"
                                                      rows="3"></textarea>
                                            @error('edit.description') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Feedback</label>
                                            <textarea wire:model.defer="edit.feedback"
                                                      class="form-control approval-control"
                                                      rows="3"></textarea>
                                            @error('edit.feedback') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    @if($selectedSchedule->status === 'cancelled' && $selectedSchedule->reason)
                                        <div class="col-12">
                                            <div class="approval-info">
                                                <label class="form-label small fw-semibold text-dark mb-1">Lý do từ chối</label>
                                                <div class="approval-desc">{{ $selectedSchedule->reason }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- File hiện tại --}}
                                    <div class="col-12">
                                        <div class="approval-filebox">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <div class="fw-bold text-dark">File đính kèm</div>
                                                <div class="small text-muted">
                                                    {{ optional($selectedSchedule->files)->count() ?? 0 }} file
                                                </div>
                                            </div>

                                            @if($selectedSchedule->files && $selectedSchedule->files->count())
                                                <div class="approval-files-grid">
                                                    @foreach($selectedSchedule->files as $f)
                                                        @php
                                                            $p = $f->file_path; 
                                                            $u = $p ? \Illuminate\Support\Facades\Storage::url($p) : '#';
                                                            $n = $f->file_name ?? 'file';
                                                        @endphp
                                                        
                                                        <div class="approval-file-item" wire:key="file-{{ $f->id }}">
                                                            <a class="approval-file-link" 
                                                               href="{{ $u != '#' ? $u : 'javascript:void(0)' }}" 
                                                               target="{{ $u != '#' ? '_blank' : '' }}" 
                                                               rel="noopener">
                                                                <div class="approval-file-ic">
                                                                    <i class="ph-file-text"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="small fw-semibold text-dark text-truncate">{{ $n }}</div>
                                                                    <div class="small text-muted">
                                                                        {{ $f->file_size ? number_format($f->file_size / 1024, 1).' KB' : 'Tệp đính kèm' }}
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-link text-danger"
                                                                    wire:click.prevent="deleteFile({{ $f->id }})">
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="small text-muted">Chưa có file.</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Thêm file mới + preview file mới --}}
                                    @if(count($newFiles) > 0)
                                        @foreach($newFiles as $idx => $file)
                                            <div class="col-12" wire:key="new-file-{{ $idx }}">
                                                <div class="approval-file-item">
                                                    <div class="approval-file-ic">
                                                        <i class="ph-paperclip"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="small fw-semibold text-dark text-truncate">
                                                            {{ $file->getClientOriginalName() }}
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                            class="btn btn-sm btn-link text-danger"
                                                            wire:click.prevent="removeNewFile({{ $idx }})">
                                                        Bỏ
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Upload file mới --}}
                                    <div class="col-12">
                                        <div class="approval-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Thêm file mới</label>
                                            <input type="file" 
                                                   wire:model="newFiles" 
                                                   class="form-control approval-control"
                                                   multiple
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                            <div class="small text-muted mt-1">
                                                Tối đa 5MB/file. Hỗ trợ: PDF, Word, Excel, hình ảnh
                                            </div>
                                            @error('newFiles.*') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <div class="d-flex w-100 justify-content-between align-items-center gap-2">
                                <button type="button" class="btn approval-btn approval-btn-ghost" @click="hideModal('details')">
                                    Đóng
                                </button>

                                <div class="d-flex gap-2">
                                    @if($selectedSchedule && $selectedSchedule->status === 'pending')
                                        <button type="button" 
                                                wire:click="approveNow({{ $selectedSchedule->id }})" 
                                                class="btn approval-btn approval-btn-success">
                                            Phê duyệt
                                        </button>
                                        <button type="button" 
                                                wire:click="confirmReject({{ $selectedSchedule->id }})" 
                                                class="btn approval-btn approval-btn-danger">
                                            Từ chối
                                        </button>
                                    @endif
                                    
                                    <button wire:click="confirmDelete" 
        type="button"
        class="btn"
        style="background-color: #ef4444; color: white; border: none;">
    Xóa
</button>

<button wire:click="updateEvent" 
        type="button"
        class="btn"
        style="background-color: #10b981; color: white; border: none;"
        wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="updateEvent">Lưu</span>
    <span wire:loading wire:target="updateEvent">Đang lưu...</span>
</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Confirm --}}
            {{-- Modal Confirm --}}
<div x-show="modals.confirm" 
     x-cloak
     @open-confirm-modal.window="showModal('confirm')"
     @close-confirm-modal.window="hideModal('confirm')"
     class="modal fade" 
     :class="{ 'show d-block': modals.confirm }"
     style="background: rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 approval-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold">{{ $confirmTitle ?: 'Xác nhận từ chối' }}</h5>
                <button type="button" class="btn-close" @click="hideModal('confirm')"></button>
            </div>
            <div class="modal-body">
                <div class="approval-info border-0 p-0">
                    <p class="text-muted">{{ $confirmMessage }}</p>
                    
                    <label class="form-label small fw-bold">Lý do từ chối <span class="text-danger">*</span></label>
                    <textarea wire:model.defer="rejectionNote" 
                              class="form-control approval-control" 
                              rows="3" 
                              placeholder="Nhập lý do cụ thể để người dùng biết..."></textarea>
                    @error('rejectionNote') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn approval-btn approval-btn-ghost" @click="hideModal('confirm')">Hủy</button>
                <button wire:click="performConfirm" 
                        class="btn approval-btn approval-btn-danger"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="performConfirm">Xác nhận từ chối</span>
                    <span wire:loading wire:target="performConfirm">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

            {{-- Modal Password --}}
            {{-- <div x-show="modals.password"
                 x-cloak
                 @open-password-modal.window="showModal('password')"
                 @close-password-modal.window="hideModal('password')"
                 class="modal fade"
                 :class="{ 'show d-block': modals.password }"
                 tabindex="-1"
                 style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold text-dark">🔑 Nhập mã phòng</h5>
                            <button type="button" class="btn-close" @click="hideModal('password')"></button>
                        </div>
                        <div class="modal-body">
                            <div class="approval-info border-0 p-0">
                                <label class="form-label small fw-bold">Mã phòng lab</label>
                                <input type="text"
                                       wire:model="roomCode"
                                       class="form-control approval-control"
                                       placeholder="Nhập mã phòng..."
                                       autofocus>
                                <div class="small text-muted mt-2">
                                    💡 Mã phòng sẽ được gửi qua email cho người dùng.
                                    @if($seriesApproveCount > 1)
                                        <br>Có {{ $seriesApproveCount }} lịch sẽ được phê duyệt.
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn approval-btn approval-btn-ghost" @click="hideModal('password')">
                                Hủy
                            </button>
                            <button wire:click="approveSchedule"
                                    type="button"
                                    class="btn approval-btn approval-btn-success"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approveSchedule">Xác nhận phê duyệt</span>
                                <span wire:loading wire:target="approveSchedule">Đang xử lý...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div> --}}
                {{-- Modal Password --}}
<div x-show="modals.password"
     x-cloak
     @open-password-modal.window="showModal('password')"
     @close-password-modal.window="hideModal('password')"
     class="modal fade"
     :class="{ 'show d-block': modals.password }"
     tabindex="-1"
     style="background: rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 approval-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark">🔑 Nhập mã phòng</h5>
                <button type="button" class="btn-close" @click="hideModal('password')"></button>
            </div>
            <div class="modal-body">
                <div class="approval-info border-0 p-0">
                    <label class="form-label small fw-bold">Mã phòng lab <span class="text-danger">*</span></label>
                    
                    <input type="text"
                           wire:model.defer="roomCode"
                           wire:keydown.enter="approveSchedule"
                           class="form-control approval-control"
                           placeholder="Nhập mã phòng..."
                           required
                           x-ref="roomCodeInput"
                           @open-password-modal.window="$nextTick(() => $refs.roomCodeInput?.focus())">
                    
                    @error('roomCode')
                        <div class="small text-danger mt-1">{{ $message }}</div>
                    @enderror
                    
                     {{-- <div class="small text-info mt-1">
                        Debug: roomCode = "<strong>{{ $roomCode }}</strong>"
                    </div> --}}
                    
                    <div class="small text-muted mt-2">
                        💡 Mã phòng sẽ được gửi qua email cho người dùng.
                        @if($seriesApproveCount > 1)
                            <br>Có {{ $seriesApproveCount }} lịch sẽ được phê duyệt.
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" 
                        class="btn approval-btn approval-btn-ghost" 
                        @click="hideModal('password')">
                    Hủy
                </button>
                <button wire:click="approveSchedule"
                        type="button"
                        class="btn approval-btn approval-btn-success"
                        wire:loading.attr="disabled"
                        wire:target="approveSchedule">
                    <span wire:loading.remove wire:target="approveSchedule">
                        Xác nhận phê duyệt
                    </span>
                    <span wire:loading wire:target="approveSchedule">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Đang xử lý...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
            {{-- Modal Conflict --}}
            <div x-show="modals.conflict"
                 x-cloak
                 @open-conflict-modal.window="showModal('conflict')"
                 @close-conflict-modal.window="hideModal('conflict')"
                 class="modal fade"
                 :class="{ 'show d-block': modals.conflict }"
                 tabindex="-1"
                 style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold text-danger">⚠ Trùng lịch với 1 lịch đã duyệt</h5>
                            <button type="button" class="btn-close" @click="hideModal('conflict')"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2 text-dark fw-semibold">
                                Khung giờ này đã có lịch khác được phê duyệt.
                            </p>

                            @if($conflictSchedule)
                                <div class="approval-info mb-3">
                                    <div class="small text-muted mb-1">Lịch đang trùng</div>
                                    <div class="fw-semibold text-dark">{{ $conflictSchedule->title }}</div>
                                    <div class="small text-muted">
                                        Phòng: {{ $conflictSchedule->lab?->name ?? 'N/A' }} ({{ $conflictSchedule->lab_code ?? '-' }})
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Thời gian: {{ optional($conflictSchedule->start)->format('H:i d/m/Y') }}
                                        – {{ optional($conflictSchedule->end)->format('H:i d/m/Y') }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Người đăng ký: {{ $conflictSchedule->user?->full_name ?? 'N/A' }}
                                    </div>
                                </div>
                            @endif
                                    {{-- Chi tiết trùng (ẩn/xổ) --}}
@if(!empty($conflictDetails['conflicts']))
    <button class="btn btn-sm approval-btn approval-btn-ghost mt-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#conflictDetailCollapse">
        Xem chi tiết lịch trùng
    </button>

    <div class="collapse mt-2" id="conflictDetailCollapse">
        <div class="approval-filebox">
            <div class="fw-bold text-dark mb-2">Trùng với các lịch sau:</div>

            <div class="d-grid gap-2">
                @foreach($conflictDetails['conflicts'] as $c)
                    <div class="approval-file-item">
                        <div class="approval-file-ic">
                            <i class="ph-warning-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold text-dark">
                                #{{ $c['id'] }} — {{ $c['title'] }}
                            </div>
                            <div class="small text-muted">
                                {{ $c['start'] }} – {{ $c['end'] }}
                                • {{ $c['lab_name'] ?? '' }} ({{ $c['lab_code'] ?? '' }})
                                @if(!empty($c['user'])) • {{ $c['user'] }} @endif
                            </div>
                        </div>
                        <span class="badge approval-pill approval-pill-approved">{{ $c['status'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

                            <p class="text-muted mb-0">
                                Bạn có chắc muốn <strong>phê duyệt</strong> và tiếp tục nhập mã phòng?
                            </p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn approval-btn approval-btn-ghost" @click="hideModal('conflict')">
                                Hủy
                            </button>
                            <button type="button"
                                    wire:click="forceApprove"
                                    class="btn approval-btn approval-btn-ghost" style ="color:#16a34a"
                                    > 
                                Vẫn phê duyệt
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
{{-- Modal Batch Conflict --}}
<div x-show="modals.batchConflict"
     x-cloak
     @open-batch-conflict-modal.window="showModal('batchConflict')"
     @close-batch-conflict-modal.window="hideModal('batchConflict')"
     class="modal fade"
     :class="{ 'show d-block': modals.batchConflict }"
     tabindex="-1"
     style="background: rgba(0,0,0,0.5)">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 approval-modal">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-semibold text-danger mb-1">⚠ Có lịch bị trùng trong danh sách đã chọn</h5>
                    <div class="small text-muted">
                        Tổng đã chọn: <b>{{ count($batchCandidateIds) }}</b> —
                        Không trùng: <b>{{ $batchOkCount }}</b> —
                        Bị trùng: <b>{{ $batchConflictCount }}</b>
                    </div>
                </div>
                <button type="button" class="btn-close" @click="hideModal('batchConflict')"></button>
            </div>

            <div class="modal-body pt-3">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="forceBatch"
                           wire:model.live="batchForceApproveConflicts">
                    <label class="form-check-label" for="forceBatch">
                        Vẫn phê duyệt cả lịch bị trùng 
                    </label>
                </div>

                <button class="btn btn-sm approval-btn approval-btn-ghost mb-2"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#batchConflictCollapse">
                    Xem danh sách lịch bị trùng
                </button>

                <div class="collapse" id="batchConflictCollapse">
                    <div class="accordion" id="batchConflictAccordion">
                        @foreach($batchConflictDetails as $idx => $row)
                            @php $e = $row['event'] ?? []; @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $idx }}">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $idx }}">
                                         {{ $e['title'] ?? '' }}
                                        ({{ $e['start'] ?? '' }} → {{ $e['end'] ?? '' }})
                                        • {{ $e['lab_code'] ?? '' }}
                                    </button>
                                </h2>

                                <div id="collapse{{ $idx }}" class="accordion-collapse collapse"
                                     data-bs-parent="#batchConflictAccordion">
                                    <div class="accordion-body">
                                        <div class="small text-muted mb-2">Trùng với:</div>

                                        <div class="d-grid gap-2">
                                            @foreach(($row['conflicts'] ?? []) as $c)
                                                <div class="approval-file-item">
                                                    <div class="approval-file-ic">
                                                        <i class="ph-warning-circle"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="small fw-semibold text-dark">
                                                             {{ $c['title'] }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $c['start'] }} – {{ $c['end'] }}
                                                            • {{ $c['lab_name'] ?? '' }} ({{ $c['lab_code'] ?? '' }})
                                                            @if(!empty($c['user'])) • {{ $c['user'] }} @endif
                                                        </div>
                                                    </div>
                                                    {{-- <span class="badge approval-pill approval-pill-approved">{{ $c['status'] }}</span> --}}
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="small text-muted mt-3">
                    Nhấn <b>Duyệt tiếp</b> để chuyển sang bước nhập mã phòng. Hệ thống sẽ duyệt theo lựa chọn ở trên.
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn approval-btn approval-btn-ghost"
                        wire:click="cancelBatchConflict"
                        @click="hideModal('batchConflict')">
                    Hủy
                </button>

                <button type="button"
                                    class="btn approval-btn approval-btn-ghost" style ="color:#16a34a"
                        wire:click="continueBatchAfterConflict">
                    Duyệt tiếp
                </button>
            </div>
        </div>
    </div>
</div>

    <style>
        [x-cloak] { display: none !important; }
        
        .approval-page {
            --ap-bg: #f8fafc;
            --ap-card: #ffffff;
            --ap-text: #0f172a;
            --ap-muted: #64748b;
            --ap-border: #e5e7eb;
            --ap-radius: 14px;
            --ap-primary: #2563eb;
            --ap-primary-soft: #eaf1ff;
            --ap-success: #16a34a;
            --ap-success-soft: #e9f9ef;
            --ap-danger: #dc2626;
            --ap-danger-soft: #fee2e2;
            --ap-warn: #f59e0b;
            --ap-warn-soft: #fff3db;
            background: var(--ap-bg);
        }

        .approval-card {
            border-radius: var(--ap-radius);
            background: var(--ap-card);
            border: 1px solid var(--ap-border);
            box-shadow: none;
        }

        .approval-badge-warn {
            background: var(--ap-warn-soft);
            color: #7a4b00;
            border-radius: 999px;
            padding: 6px 12px;
            font-weight: 600;
            border: 1px solid rgba(245, 158, 11, .22);
        }

        .approval-filters {
            background: #fff;
            border: 1px solid var(--ap-border);
            border-radius: 12px;
            padding: 12px;
        }

        .approval-control {
            border: 1px solid var(--ap-border) !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            font-size: 0.9rem;
        }

        .approval-table-wrap {
            border: 1px solid var(--ap-border);
            border-radius: 12px;
            background: #fff;
            overflow-x: visible;
            overflow-y: visible;
            position: relative;
        }

        .approval-table thead th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            padding: 12px;
            border-bottom: 1px solid var(--ap-border);
        }

        .approval-table tbody td {
            padding: 12px;
            border-top: 1px solid var(--ap-border);
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .approval-pill {
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: .78rem;
        }

        .approval-pill-pending {
            background: var(--ap-warn-soft);
            color: #7a4b00;
        }

        .approval-pill-approved {
            background: var(--ap-success-soft);
            color: #166534;
        }

        .approval-pill-cancelled {
            background: var(--ap-danger-soft);
            color: #b91c1c;
        }

        .approval-btn {
            border-radius: 8px;
            padding: 7px 14px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .approval-btn-success {
            background: var(--ap-success);
            color: #fff;
            border: 1px solid var(--ap-success);
        }

        .approval-btn-danger {
            background: var(--ap-danger-soft);
            color: var(--ap-danger);
            border: 1px solid #fecaca;
        }

        .approval-btn-ghost {
            background: #fff;
            border: 1px solid var(--ap-border);
            color: var(--ap-muted);
        }

        .approval-info {
            border: none;
            border-radius: 0;
            padding: 0;
            background: transparent;
        }

        .approval-filebox {
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid var(--ap-border);
            padding: 10px 12px;
        }

        .approval-files-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .approval-file-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 8px;
            border-radius: 8px;
            border: 1px solid var(--ap-border);
            background: #fff;
        }

        .approval-file-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            flex: 1;
        }

        .approval-file-ic {
            background: var(--ap-primary-soft);
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .text-wrap-mobile {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 260px;
        }

        .text-center-desktop {
            text-align: center;
        }

        .action-cell {
            text-align: center;
        }

        .approval-action-dropdown {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .approval-action-toggle {
            padding: 0;
            line-height: 1;
            color: #2563eb;
        }

        .approval-action-toggle i {
            font-size: 18px;
        }

        .approval-action-dropdown .dropdown-menu {
            border-radius: 8px;
            font-size: 0.86rem;
            min-width: 140px;
        }

        .approval-modal {
            border-radius: 14px;
            overflow: hidden;
        }

        .approval-desc {
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            white-space: pre-line;
            border: 1px solid var(--ap-border);
            color: var(--ap-text);
        }

        @media (max-width: 991.98px) {
            .approval-table,
            .approval-table thead,
            .approval-table tbody,
            .approval-table tr,
            .approval-table td {
                display: block;
                width: 100%;
            }

            .approval-table thead {
                display: none;
            }

            .approval-table tbody tr {
                margin-bottom: 1.2rem;
                border: 1px solid var(--ap-border) !important;
                border-radius: 12px;
                background: #fff;
                position: relative;
            }

            .approval-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 10px 14px !important;
                border-bottom: 1px solid var(--ap-border) !important;
                border-top: none !important;
            }

            .approval-table tbody td:last-child {
                border-bottom: none !important;
                background: #f8fbff;
            }

            .approval-table td::before {
                content: attr(data-label);
                float: left;
                font-weight: 600;
                color: #64748b;
                font-size: 0.75rem;
                text-transform: uppercase;
            }

            .text-wrap-mobile {
                white-space: normal !important;
                text-align: right;
                max-width: 100% !important;
            }

            .approval-table td[data-label="Sự kiện"] {
                background: var(--ap-primary-soft);
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .approval-table td[data-label="Sự kiện"]::before {
                margin-bottom: 4px;
            }
        }
    </style>

    <script>
        function approvalComponent() {
            return {
                modals: {
                    details: false,
                    confirm: false,
                    password: false,
                    conflict: false,
                    batchConflict: false,
                },
                
                showModal(type) {
                    this.modals[type] = true;
                    document.body.classList.add('modal-open');
                },
                
                hideModal(type) {
                    this.modals[type] = false;
                    document.body.classList.remove('modal-open');
                }
            }
        }
    </script>
</div>