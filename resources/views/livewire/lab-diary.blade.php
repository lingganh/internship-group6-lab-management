<div>
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">Nhật ký sử dụng</h4>
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
                    <span class="breadcrumb-item active">Nhật ký sử dụng</span>
                </div>
            </div>
        </div>
    </div>

    <div id="toastPayload"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-warning="{{ session('warning') }}"
         data-info="{{ session('info') }}"
         style="display:none"></div>

    <div class="container-fluid py-4 diary-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-11">
                <div class="card border-0 diary-card">
                    <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between ">
                        <h4 class="mb-0 fw-bold text-dark">Nhật ký sử dụng</h4>
                        <button wire:click="export"class="btn btn-success">
                            <i class="ph ph-microsoft-excel-logo"></i>
                            Xuất file Excel
                        </button>
                    </div>

                    <div class="card-body pt-3">
                        <div class="diary-filters mb-3">
                            <div class="row g-2 g-md-3 align-items-end">
                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                    <select wire:model.live="filterLabCode" class="form-select diary-control">
                                        <option value="">Tất cả</option>
                                        @foreach($labs as $lab)
                                            <option wire:key="lab-{{ $lab->code }}" value="{{ $lab->code }}">
                                                {{ $lab->name }} ({{ $lab->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 col-md-2">
                                    <label class="form-label small fw-semibold text-dark mb-1">Từ ngày</label>
                                    <input type="date"
                                           wire:model.live="filterFrom"
                                           class="form-control diary-control">
                                </div>

                                <div class="col-6 col-md-2">
                                    <label class="form-label small fw-semibold text-dark mb-1">Đến ngày</label>
                                    <input type="date"
                                           wire:model.live="filterTo"
                                           class="form-control diary-control">
                                </div>

                                <div class="col-12 col-md-5">
                                    <label class="form-label small fw-semibold text-dark mb-1">Từ khóa</label>
                                    <input type="text"
                                           wire:model.live="keyword"
                                           class="form-control diary-control"
                                           placeholder="Tiêu đề / mô tả / feedback...">
                                </div>
                            </div>
                        </div>

                        <div class="diary-table-wrap">
                            <table class="table align-middle mb-0 diary-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 250px;">Nội dung</th>
                                        <th>Phòng</th>
                                        <th>Người đăng ký</th>
                                        <th>Thời gian</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $item)
                                        <tr wire:key="event-{{ $item->id }}">
                                            <td data-label="Nội dung">
                                                <div class="fw-bold text-dark text-wrap-mobile">
                                                    {{ $item->title }}
                                                </div>
                                                <div class="small text-muted">
                                                    #{{ $item->id }} • {{ $this->categoryLabel($item->category) }}
                                                </div>
                                            </td>

                                            <td data-label="Phòng">
                                                <div class="fw-semibold text-dark">
                                                    {{ $item->lab?->name ?? ($item->lab_code ?? 'N/A') }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->lab_code ?? '-' }}
                                                </div>
                                            </td>

                                            <td data-label="Người đăng ký">
                                                <div class="fw-semibold text-dark">
                                                    {{ $item->user?->full_name ?? 'N/A' }}
                                                </div>
                                                <div class="small text-muted text-break">
                                                    {{ $item->user?->email ?? '' }}
                                                </div>
                                            </td>

                                            <td data-label="Thời gian">
                                                <div class="fw-semibold text-dark">
                                                    {{ $item->start->format('d/m/Y') }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->start->format('H:i') }} – {{ $item->end->format('H:i') }}
                                                </div>
                                            </td>

                                            <td data-label="Trạng thái" class="text-center-desktop">
                                                @if($item->status === 'pending')
                                                    <span class="badge diary-pill diary-pill-pending">
                                                        Chờ duyệt
                                                    </span>
                                                @elseif($item->status === 'approved')
                                                    <span class="badge diary-pill diary-pill-approved">
                                                        Đã duyệt
                                                    </span>
                                                @elseif($item->status === 'completed')
                                                    <span class="badge diary-pill diary-pill-approved">
                                                        Đã hoàn thành
                                                    </span>
                                                @else
                                                    <span class="badge diary-pill diary-pill-cancelled">
                                                        Từ chối
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-end-desktop action-cell">
                                                <button
                                                    wire:click="viewEvent({{ $item->id }})"
                                                    class="btn btn-sm diary-btn diary-btn-primary w-100-mobile"
                                                    type="button">
                                                    Chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    Không có dữ liệu phù hợp.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                            <div class="mt-3 d-flex justify-content-center">
                                {{ $events->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        {{-- Toast --}}
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
            <div id="apToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-2">
                    <div id="apToastIcon" class="ap-toast-ic"></div>
                    <div class="flex-grow-1">
                        <div id="apToastMsg" class="fw-semibold text-dark"></div>
                        <div id="apToastSub" class="small text-muted mt-1"></div>
                    </div>
                    <button type="button"
                            class="btn-close ms-2 mt-1"
                            data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        </div>

            {{-- Modal Details --}}
            <div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 diary-modal">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-semibold text-dark mb-1">Chi tiết nhật ký</h5>
                                <div class="small text-muted">Xem và chỉnh sửa nhật ký sử dụng phòng lab.</div>
                            </div>
                            <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                    <div class="modal-body pt-3">
                        @if($selectedEvent)
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                                        <input wire:model.defer="edit.title" type="text" class="form-control diary-control">
                                        @error('edit.title') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Phân loại</label>
                                        <select wire:model.defer="edit.category" class="form-select diary-control">
                                            <option value="work">Làm việc / nghiên cứu</option>
                                            <option value="seminar">Hội thảo / seminar</option>
                                            <option value="other">Khác</option>
                                        </select>
                                        @error('edit.category') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                        <select wire:model.defer="edit.lab_code" class="form-select diary-control">
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
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Bắt đầu</label>
                                        <input wire:model.defer="edit.start" type="datetime-local" class="form-control diary-control">
                                        @error('edit.start') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-6 col-md-3">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Kết thúc</label>
                                        <input wire:model.defer="edit.end" type="datetime-local" class="form-control diary-control">
                                        @error('edit.end') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                                        <select wire:model.defer="edit.status" class="form-select diary-control">
                                            <option value="pending">Chờ duyệt</option>
                                            <option value="approved">Đã duyệt</option>
                                            <option value="cancelled">Từ chối</option>
                                            <option value="completed">Đã hoàn thành</option>
                                        </select>
                                        @error('edit.status') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Đăng ký bởi</label>
                                        <select wire:model.defer="edit.user_id" class="form-select diary-control">
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
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Đăng ký cho nhóm</label>
                                        <select wire:model.defer="edit.group_id" class="form-select diary-control">
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
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                        <textarea wire:model.defer="edit.description"
                                                  class="form-control diary-control"
                                                  rows="3"></textarea>
                                        @error('edit.description') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Feedback</label>
                                        <textarea wire:model.defer="edit.feedback"
                                                  class="form-control diary-control"
                                                  rows="3"></textarea>
                                        @error('edit.feedback') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- File hiện tại --}}
                                <div class="col-12">
                                    <div class="diary-filebox">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <div class="fw-bold text-dark">File đính kèm</div>
                                            <div class="small text-muted">
                                                {{ optional($selectedEvent->files)->count() ?? 0 }} file
                                            </div>
                                        </div>

                                        @if($selectedEvent->files && $selectedEvent->files->count())
                                            <div class="diary-files">
                                                @foreach($selectedEvent->files as $f)
                                                    @php
                                                        $p = $f->path ?? $f->file_path ?? $f->url ?? '';
                                                        $u = $p ? \Illuminate\Support\Facades\Storage::url($p) : '#';
                                                        $n = $f->name ?? $f->file_name ?? $f->original_name ?? basename((string)$p) ?? 'file';
                                                    @endphp
                                                    <div class="diary-file">
                                                        <a class="diary-file-link" href="{{ $u }}" target="_blank" rel="noopener">
                                                            <div class="diary-file-ic">
                                                                <i class="ph-file-text"></i>
                                                            </div>
                                                            <div>
                                                                <div class="small fw-semibold text-dark text-truncate">
                                                                    {{ $n }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    {{ $f->file_size ? number_format($f->file_size / 1024, 1).' KB' : 'Tệp đính kèm' }}
                                                                </div>
                                                            </div>
                                                        </a>

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-link text-danger diary-file-delete"
                                                            wire:click="deleteFile({{ $f->id }})">
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
                                <div class="col-12">
                                    <div class="diary-info">
                                        <label class="form-label small fw-semibold text-dark mb-1">Thêm file mới</label>
                                        <input type="file" wire:model="newFiles" multiple class="form-control diary-control">
                                        <div class="small text-muted mt-1">
                                            Có thể chọn nhiều file cùng lúc. File mới sẽ được lưu khi bấm <b>Lưu</b>
                                        </div>
                                        @error('newFiles') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                                        @error('newFiles.*') <div class="small text-danger mt-1">{{ $message }}</div> @enderror

                                        @if($newFiles && count($newFiles))
                                            <div class="diary-newfiles mt-2">
                                                @foreach($newFiles as $idx => $file)
                                                    <div class="diary-file">
                                                        <div class="diary-file-ic">
                                                            <i class="ph-paperclip"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="small fw-semibold text-dark text-truncate">
                                                                {{ $file->getClientOriginalName() }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                {{ number_format($file->getSize() / 1024, 1) }} KB
                                                            </div>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-link text-danger diary-file-delete"
                                                            wire:click="removeNewFile({{ $idx }})">
                                                            Bỏ
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                        <div class="modal-footer border-0 pt-0">
                            <div class="d-flex w-100 justify-content-between align-items-center gap-2">
                                <button type="button" class="btn diary-btn diary-btn-ghost" data-bs-dismiss="modal">
                                    Đóng
                                </button>

                                <div class="d-flex gap-2">
                                    <button wire:click="openDeleteConfirm" type="button"
                                        class="btn diary-btn diary-btn-danger">
                                        Xóa
                                    </button>

                                    <button wire:click="updateEvent" type="button"
                                        class="btn diary-btn diary-btn-success">
                                        Lưu
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        {{-- Modal Confirm --}}
        <div wire:ignore.self
             class="modal fade"
             id="modalConfirm"
             tabindex="-1"
             aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 diary-modal">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark mb-1">
                            Xác nhận xóa
                        </h5>
                    </div>
                    <div class="modal-body py-3 text-muted">
                        Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa?
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button"
                                class="btn diary-btn diary-btn-ghost"
                                data-bs-dismiss="modal">
                            Hủy
                        </button>
                        <button
                            wire:click="deleteEvent"
                            type="button"
                            class="btn diary-btn diary-btn-danger">
                            Xác nhận xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <style>
            .diary-page {
                --d-bg: #f8fafc;
                --d-card: #ffffff;
                --d-text: #0f172a;
                --d-muted: #6b7280;
                --d-border: #e5e7eb;
                --d-radius: 14px;
                --d-primary: #2563eb;
                --d-success: #16a34a;
                --d-danger: #dc2626;
                --d-warn: #f59e0b;
                background: var(--d-bg);
                min-height: 100vh;
            }

            .diary-card {
                border-radius: var(--d-radius);
                background: var(--d-card);
                border: 1px solid var(--d-border);
                box-shadow: none;
            }

            .diary-filters {
                background: #f9fafb;
                border: 1px solid var(--d-border);
                border-radius: 12px;
                padding: 12px;
            }

            .diary-control {
                border: 1px solid var(--d-border) !important;
                border-radius: 8px !important;
                padding: 8px 10px !important;
                font-size: 0.9rem;
            }

            .diary-table-wrap {
                border: 1px solid var(--d-border);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            .diary-table thead th {
                background: #f9fafb;
                color: #374151;
                font-weight: 600;
                padding: 12px;
                font-size: .9rem;
                border-bottom: 1px solid var(--d-border);
            }

            .diary-table tbody td {
                padding: 12px;
                border-top: 1px solid var(--d-border);
                font-size: 0.9rem;
            }

            .diary-pill {
                border-radius: 999px;
                padding: 4px 10px;
                font-weight: 600;
                font-size: .78rem;
                display: inline-flex;
            }

        .diary-pill-pending { background: var(--d-warn-soft); color: #7a4b00; }
        .diary-pill-approved { background: var(--d-success-soft); color: #0f6a2e; }
        .diary-pill-cancelled { background: var(--d-danger-soft); color: #8a1414; }

            .diary-btn {
                border-radius: 8px;
                padding: 7px 14px;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .diary-btn-primary {
                background: #eff6ff;
                color: var(--d-primary);
                border: 1px solid #dbeafe;
            }

            .diary-btn-success {
                background: var(--d-success);
                color: #fff;
                border: 1px solid var(--d-success);
            }

            .diary-btn-danger {
                background: #fee2e2;
                color: var(--d-danger);
                border: 1px solid #fecaca;
            }

            .diary-btn-ghost {
                background: #fff;
                border: 1px solid var(--d-border);
                color: var(--d-muted);
            }

        .text-center-desktop { text-align: center; }
        .text-end-desktop { text-align: right; }

            .text-wrap-mobile {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 320px;
            }

            .diary-info {
                border: none;
                border-radius: 0;
                padding: 0;
                background: transparent;
            }

            .diary-filebox {
                background: #f9fafb;
                border-radius: 10px;
                border: 1px solid var(--d-border);
                padding: 10px 12px;
            }

            .diary-files,
            .diary-newfiles {
                display: grid;
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .diary-file {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 8px;
                border-radius: 8px;
                border: 1px solid var(--d-border);
                background: #fff;
            }

            .diary-file-link {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                color: inherit;
                flex: 1;
            }

            .diary-file-ic {
                background: #eff6ff;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
            }

            .diary-file-delete {
                font-size: 0.78rem;
                white-space: nowrap;
                padding-right: 0;
            }

            .diary-modal {
                border-radius: 14px;
                overflow: hidden;
            }

            /* menu hành động đơn giản */
            .diary-action-dropdown .dropdown-menu {
                font-size: 0.86rem;
            }

            .diary-action-toggle {
                padding: 0;
                line-height: 1;
                color: #6b7280;
            }

            .diary-action-toggle i {
                font-size: 18px;
            }

        @media (max-width: 991.98px) {
            .diary-table,
            .diary-table thead,
            .diary-table tbody,
            .diary-table tr,
            .diary-table td {
                display: block;
                width: 100%;
            }

            .diary-table thead { display: none; }

                .diary-table tbody tr {
                    margin-bottom: 0.75rem;
                    border: 1px solid var(--d-border) !important;
                    border-radius: 12px;
                    overflow: hidden;
                    background: #fff;
                }

                .diary-table tbody td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    text-align: right;
                    padding: 9px 14px !important;
                    border-bottom: 1px solid var(--d-border) !important;
                    border-top: none !important;
                }

                .diary-table tbody td:last-child {
                    border-bottom: none !important;
                }

                .diary-table td::before {
                    content: attr(data-label);
                    float: left;
                    font-weight: 600;
                    color: var(--d-muted);
                    font-size: 0.75rem;
                    text-transform: uppercase;
                }

                .text-wrap-mobile {
                    white-space: normal !important;
                    text-align: right;
                    max-width: 100% !important;
                    font-size: 0.96rem;
                }

                .diary-table td[data-label="Nội dung"] {
                    flex-direction: column;
                    align-items: flex-start;
                    text-align: left;
                }

                .diary-table td[data-label="Nội dung"]::before {
                    margin-bottom: 4px;
                }

            .w-100-mobile { width: 100%; }
            .diary-page { padding: 10px !important; }
        }

            .ap-toast-ic {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #eff6ff;
                color: var(--d-primary);
                font-weight: 600;
            }
        </style>

    <script>
        function apToast(type, msg, sub) {
            const toastEl = document.getElementById('apToast');
            if (!toastEl) return;
            document.getElementById('apToastMsg').textContent = msg;
            document.getElementById('apToastSub').textContent = sub || '';
            const icon = document.getElementById('apToastIcon');
            icon.textContent = type === 'success'
                ? '✓'
                : (type === 'error' ? '!' : 'i');
            bootstrap.Toast.getOrCreateInstance(toastEl).show();
        }

            window.addEventListener('open-details-modal', () =>
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetails')).show()
            );

            window.addEventListener('close-details-modal', () =>
                bootstrap.Modal.getInstance(document.getElementById('modalDetails'))?.hide()
            );

            window.addEventListener('open-confirm-modal', () =>
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirm')).show()
            );

        window.addEventListener('close-confirm-modal', () =>
            bootstrap.Modal.getInstance(document.getElementById('modalConfirm'))?.hide()
        );
    </script>
</div>
