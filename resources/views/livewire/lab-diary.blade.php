<div x-data="diaryComponent()">
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

    <div class="container-fluid py-4 diary-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-11">
                <div class="card border-0 diary-card">
                    <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between">
                        <h4 class="mb-0 fw-bold text-dark">Nhật ký sử dụng</h4>
                        <button wire:click="export" 
                                class="btn btn-success"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="export">
                                <i class="ph ph-microsoft-excel-logo"></i>
                                Xuất file Excel
                            </span>
                            <span wire:loading wire:target="export">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Đang xuất...
                            </span>
                        </button>
                    </div>

                    <div class="card-body pt-3">
                        <div class="diary-filters mb-3">
                            <div class="row g-2 g-md-3 align-items-end">
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
                                           placeholder="Tiêu đề / mô tả ...">
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
                                                    {{ $this->categoryLabel($item->category) }}
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
                                                    <span class="badge diary-pill diary-pill-pending">Chờ duyệt</span>
                                                @elseif($item->status === 'approved')
                                                    <span class="badge diary-pill diary-pill-approved">Đã duyệt</span>
                                                @elseif($item->status === 'completed')
                                                    <span class="badge diary-pill diary-pill-approved">Đã hoàn thành</span>
                                                @else
                                                    <span class="badge diary-pill diary-pill-cancelled">Từ chối</span>
                                                @endif
                                            </td>

                                            <td class="text-end-desktop action-cell">
                                                <button wire:click="viewEvent({{ $item->id }})"
                                                        class="btn btn-sm diary-btn diary-btn-primary w-100-mobile"
                                                        type="button">
                                                    Chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">Không có dữ liệu phù hợp.</div>
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

            {{-- Modal Details --}}
            <div x-show="$wire.selectedEvent !== null"
                 x-cloak
                 @open-details-modal.window="showModal('details')"
                 @close-details-modal.window="hideModal('details')"
                 class="modal fade"
                 :class="{ 'show d-block': modals.details }"
                 tabindex="-1"
                 style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 diary-modal">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-semibold text-dark mb-1">Chi tiết nhật ký</h5>
                                <div class="small text-muted">Xem và chỉnh sửa nhật ký sử dụng phòng lab.</div>
                            </div>
                            <button type="button" class="btn-close mt-1" @click="hideModal('details')"></button>
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
                                            <label class="form-label small fw-semibold text-dark mb-1">Ý kiến sử dụng phòng</label>
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
                                                            $p = $f->file_path; 
                                                            $u = $p ? \Illuminate\Support\Facades\Storage::url($p) : '#';
                                                            $n = $f->file_name ?? 'file';
                                                        @endphp
                                                        
                                                        <div class="diary-file" wire:key="file-{{ $f->id }}">
                                                            <a class="diary-file-link" 
                                                               href="{{ $u != '#' ? $u : 'javascript:void(0)' }}" 
                                                               target="{{ $u != '#' ? '_blank' : '' }}" 
                                                               rel="noopener">
                                                                <div class="diary-file-ic">
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
                                                <div class="diary-file">
                                                    <div class="diary-file-ic">
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
                                        <div class="diary-info">
                                            <label class="form-label small fw-semibold text-dark mb-1">Thêm file mới</label>
                                            <input type="file" 
                                                   wire:model="newFiles" 
                                                   class="form-control diary-control"
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
                                <button type="button" class="btn diary-btn diary-btn-ghost" @click="hideModal('details')">
                                    Đóng
                                </button>

                                <div class="d-flex gap-2">
                                    <button wire:click="openDeleteConfirm" 
                                            type="button"
                                            class="btn diary-btn diary-btn-danger">
                                        Xóa
                                    </button>

                                    <button wire:click="updateEvent" 
                                            type="button"
                                            class="btn diary-btn diary-btn-success"
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
            <div x-show="modals.confirm"
                 x-cloak
                 @open-confirm-modal.window="showModal('confirm')"
                 @close-confirm-modal.window="hideModal('confirm')"
                 class="modal fade"
                 :class="{ 'show d-block': modals.confirm }"
                 tabindex="-1"
                 style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content border-0 diary-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark mb-1">Xác nhận xóa</h5>
                        </div>
                        <div class="modal-body py-3 text-muted">
                            Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa?
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn diary-btn diary-btn-ghost" @click="hideModal('confirm')">
                                Hủy
                            </button>
                            <button wire:click="deleteEvent"
                                    type="button"
                                    class="btn diary-btn diary-btn-danger"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="deleteEvent">Xác nhận xóa</span>
                                <span wire:loading wire:target="deleteEvent">Đang xóa...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        .diary-page {
            --d-bg: #f8fafc;
            --d-card: #ffffff;
            --d-text: #0f172a;
            --d-muted: #6b7280;
            --d-border: #e5e7eb;
            --d-radius: 14px;
            --d-primary: #2563eb;
            --d-success: #16a34a;
            --d-success-soft: #e9f9ef;
            --d-danger: #dc2626;
            --d-danger-soft: #fee2e2;
            --d-warn: #f59e0b;
            --d-warn-soft: #fff3db;
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

        .diary-modal {
            border-radius: 14px;
            overflow: hidden;
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
    </style>

    <script>
        function diaryComponent() {
            return {
                modals: {
                    details: false,
                    confirm: false
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