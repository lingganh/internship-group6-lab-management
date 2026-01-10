<div>
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
                                        <option value="cancelled">Đã từ chối</option>
                                        <option value="completed">Đã hoàn thành</option>
                                        <option value="">Tất cả</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                    <select wire:model.live="filterLabCode" class="form-select approval-control">
                                        <option value="">Tất cả phòng</option>
                                        @foreach($labs as $lab)
                                            <option value="{{ $lab->code }}">{{ $lab->name }}</option>
                                        @endforeach
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
                                        <tr>
                                            <td data-label="Sự kiện">
                                                <div class="fw-semibold text-dark text-wrap-mobile">
                                                    {{ $item->title }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->category }}
                                                </div>
                                            </td>

                                            <td data-label="Mã phòng">
                                                <div class="fw-semibold text-dark">
                                                    {{ $item->lab_code ?? '' }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $item->lab?->name }}
                                                </div>
                                            </td>

                                            <td data-label="Người đăng ký">
                                                <div class="fw-semibold text-dark">
                                                    {{ $item->user?->full_name ?? '' }}
                                                </div>
                                                <div class="small text-muted text-break">
                                                    ID: {{ $item->user_id }}
                                                </div>
                                            </td>

                                            <td data-label="Thời gian">
                                                <div class="fw-semibold text-dark">
                                                    {{ optional($item->start)->format('d/m/Y') }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ optional($item->start)->format('H:i') }}
                                                    –
                                                    {{ optional($item->end)->format('H:i') }}
                                                </div>
                                            </td>

                                            <td data-label="Trạng thái" class="text-center-desktop">
                                                @if($item->status === 'pending')
                                                    <span class="badge approval-pill approval-pill-pending">
                                                        Chờ duyệt
                                                    </span>
                                                @elseif($item->status === 'approved')
                                                    <span class="badge approval-pill approval-pill-approved">
                                                        Đã duyệt
                                                    </span>
                                                @elseif($item->status === 'completed')
                                                    <span class="badge approval-pill approval-pill-approved">
                                                        Đã hoàn thành
                                                    </span>
                                                @else
                                                    <span class="badge approval-pill approval-pill-cancelled">
                                                        Từ chối
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center action-cell" data-label="Hành động">
                                                <div class="dropdown approval-action-dropdown">
                                                    <button
                                                        class="btn btn-sm btn-link approval-action-toggle"
                                                        type="button"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="ph-dots-three-outline"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li>
                                                            <button
                                                                type="button"
                                                                class="dropdown-item"
                                                                wire:click="viewSchedule({{ $item->id }})">
                                                                Chi tiết
                                                            </button>
                                                        </li>

                                                        @if($item->status === 'pending')
                                                            <li>
                                                                <button
                                                                    type="button"
                                                                    class="dropdown-item"
                                                                    wire:click="approveNow({{ $item->id }})">
                                                                    Phê duyệt
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button
                                                                    type="button"
                                                                    class="dropdown-item"
                                                                    wire:click="confirmReject({{ $item->id }})">
                                                                    Từ chối
                                                                </button>
                                                            </li>
                                                        @endif

                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button
                                                                type="button"
                                                                class="dropdown-item text-danger"
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
                                            <td colspan="7" class="text-center py-5">
                                                Không có dữ liệu phù hợp.
                                            </td>
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

            <div wire:ignore.self
                 class="modal fade"
                 id="modalDetails"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-semibold text-dark mb-1">
                                    Chi tiết đăng ký
                                </h5>
                                <div class="small text-muted">
                                    Xem thông tin và xử lý yêu cầu (nếu đang chờ).
                                </div>
                            </div>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>

                        <div class="modal-body pt-3">
                            @if($selectedSchedule)
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Tiêu đề / Phân loại
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ $selectedSchedule->title }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                {{ $selectedSchedule->category }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Phòng lab
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ $selectedSchedule->lab?->name ?? 'N/A' }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                Mã: {{ $selectedSchedule->lab_code ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Người đăng ký
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ $selectedSchedule->user?->full_name ?? 'N/A' }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                {{ $selectedSchedule->user?->email }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Đăng ký cho nhóm (nếu có)
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ $selectedSchedule->group?->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Trạng thái
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                @if($selectedSchedule->status === 'pending')
                                                    Chờ duyệt
                                                @elseif($selectedSchedule->status === 'approved')
                                                    Đã duyệt
                                                @elseif($selectedSchedule->status === 'completed')
                                                    Đã hoàn thành
                                                @else
                                                    Từ chối
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Thời gian
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ optional($selectedSchedule->start)->format('H:i d/m/Y') }}
                                                <span class="text-muted">—</span>
                                                {{ optional($selectedSchedule->end)->format('H:i d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Mô tả
                                            </div>
                                            <div class="approval-desc">
                                                {{ $selectedSchedule->description ?? 'Không có mô tả.' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($selectedSchedule && $selectedSchedule->status === 'cancelled')
                                        <div class="col-12">
                                            <div class="approval-info">
                                                <div class="small text-muted mb-1">
                                                    Lý do từ chối
                                                </div>
                                                <div class="approval-desc">
                                                    {{ $selectedSchedule->reason ?: 'Không có lý do được lưu.' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="approval-info">
                                            <div class="small text-muted mb-1">
                                                Tệp đính kèm
                                            </div>

                                            @if($selectedSchedule->files && $selectedSchedule->files->count())
                                                <div class="approval-files">
                                                    @foreach($selectedSchedule->files as $file)
                                                        <a href="{{ $file->file_url ?? ($file->path ?? '#') }}"
                                                           target="_blank"
                                                           class="approval-file">
                                                            <div class="approval-file-ic">
                                                                <i class="ph-file-text"></i>
                                                            </div>
                                                            <div>
                                                                <div class="small fw-semibold text-dark text-truncate">
                                                                    {{ $file->original_name ?? basename($file->path ?? '') }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    {{ $file->file_size ? number_format($file->file_size / 1024, 1) . ' KB' : 'Tệp đính kèm' }}
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="small text-muted">
                                                    Không có tệp đính kèm.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button"
                                    class="btn approval-btn approval-btn-ghost"
                                    data-bs-dismiss="modal">
                                Đóng
                            </button>

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
                        </div>
                    </div>
                </div>
            </div>

            <div wire:ignore.self
                 class="modal fade"
                 id="modalConfirm"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold text-dark">
                                {{ $confirmTitle ?: 'Xác nhận' }}
                            </h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if($confirmType === 'reject')
                                <div class="approval-info border-0 p-0">
                                    <label class="form-label small fw-bold">
                                        Lý do từ chối
                                    </label>
                                    <textarea
                                        wire:model.defer="rejectionNote"
                                        class="form-control approval-control"
                                        rows="3"
                                        placeholder="Nhập lý do..."></textarea>
                                    <div class="small text-muted mt-2">
                                        💡 Lý do này sẽ được lưu vào lịch và gửi cho người đăng ký.
                                    </div>
                                </div>
                            @elseif($confirmType === 'delete')
                                <p class="mb-0 text-muted">
                                    {{ $confirmMessage ?: 'Bạn có chắc chắn muốn xóa lịch này? Hành động này không thể hoàn tác.' }}
                                </p>
                            @endif
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button"
                                    class="btn approval-btn approval-btn-ghost"
                                    data-bs-dismiss="modal">
                                Hủy
                            </button>
                            <button wire:click="performConfirm"
                                    type="button"
                                    class="btn approval-btn approval-btn-danger">
                                @if($confirmType === 'delete')
                                    Xác nhận xóa
                                @else
                                    Xác nhận từ chối
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div wire:ignore.self
                 class="modal fade"
                 id="modalPassword"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold text-dark">
                                🔑 Nhập mã phòng
                            </h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="approval-info border-0 p-0">
                                <label class="form-label small fw-bold">
                                    Mã phòng lab
                                </label>
                                <input type="text"
                                       wire:model="roomCode"
                                       class="form-control approval-control"
                                       placeholder="Nhập mã phòng..."
                                       autofocus>
                                <div class="small text-muted mt-2">
                                    💡 Mã phòng sẽ được gửi qua email cho người dùng.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button"
                                    class="btn approval-btn approval-btn-ghost"
                                    data-bs-dismiss="modal">
                                Hủy
                            </button>
                            <button wire:click="approveSchedule"
                                    type="button"
                                    class="btn approval-btn approval-btn-success">
                                Xác nhận phê duyệt
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div wire:ignore.self
                 class="modal fade"
                 id="modalConflict"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 approval-modal">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-semibold text-danger">
                                ⚠ Trùng lịch với 1 lịch đã duyệt
                            </h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2 text-dark fw-semibold">
                                Khung giờ này đã có lịch khác được phê duyệt.
                            </p>

                            @if($conflictSchedule)
                                <div class="approval-info mb-3">
                                    <div class="small text-muted mb-1">
                                        Lịch đang trùng
                                    </div>
                                    <div class="fw-semibold text-dark">
                                        {{ $conflictSchedule->title }}
                                    </div>
                                    <div class="small text-muted">
                                        Phòng:
                                        {{ $conflictSchedule->lab?->name ?? 'N/A' }}
                                        ({{ $conflictSchedule->lab_code ?? '-' }})
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Thời gian:
                                        {{ optional($conflictSchedule->start)->format('H:i d/m/Y') }}
                                        –
                                        {{ optional($conflictSchedule->end)->format('H:i d/m/Y') }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Người đăng ký:
                                        {{ $conflictSchedule->user?->full_name ?? 'N/A' }}
                                    </div>
                                </div>
                            @endif

                            <p class="text-muted mb-0">
                                Bạn có chắc muốn <strong> phê duyệt</strong> và tiếp tục nhập mã phòng?
                            </p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button"
                                    class="btn approval-btn approval-btn-ghost"
                                    data-bs-dismiss="modal">
                                Hủy
                            </button>
                            <button type="button"
                                    wire:click="forceApprove"
                                    class="btn approval-btn approval-btn-success">
                                Vẫn phê duyệt
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
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
            overflow-x: auto;
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
            border: 1px solid var(--ap-border);
            border-radius: 10px;
            padding: 10px;
            background: #fff;
        }

        .approval-desc {
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            white-space: pre-line;
            border: 1px solid var(--ap-border);
        }

        .approval-files {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .approval-file {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 8px;
            border-radius: 8px;
            border: 1px solid var(--ap-border);
            text-decoration: none;
            color: inherit;
            background: #fff;
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
        function apGetModal(id) {
            const el = document.getElementById(id);
            if (!el) return null;
            return bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static' });
        }

        window.addEventListener('open-details-modal', () =>
            apGetModal('modalDetails')?.show()
        );

        window.addEventListener('close-details-modal', () =>
            bootstrap.Modal.getInstance(document.getElementById('modalDetails'))?.hide()
        );

        window.addEventListener('open-confirm-modal', () =>
            apGetModal('modalConfirm')?.show()
        );

        window.addEventListener('close-confirm-modal', () =>
            bootstrap.Modal.getInstance(document.getElementById('modalConfirm'))?.hide()
        );

        window.addEventListener('open-password-modal', () =>
            apGetModal('modalPassword')?.show()
        );

        window.addEventListener('close-password-modal', () =>
            bootstrap.Modal.getInstance(document.getElementById('modalPassword'))?.hide()
        );

        window.addEventListener('open-conflict-modal', () =>
            apGetModal('modalConflict')?.show()
        );

        window.addEventListener('close-conflict-modal', () =>
            bootstrap.Modal.getInstance(document.getElementById('modalConflict'))?.hide()
        );
    </script>
</div>
