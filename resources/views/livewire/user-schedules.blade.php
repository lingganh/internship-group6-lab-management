<div>
    <x-slot name="header">
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('home')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <span class="breadcrumb-item active">Lịch đã đăng ký </span>
                    </div>
                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </x-slot>
    <div class="container py-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h5 class="fw-semibold text-dark m-0">Lịch đã đăng ký</h5>

           <div class="col-12 col-lg-8">
        <div class="row g-2 justify-content-lg-end">
            {{-- Ô tìm kiếm --}}
            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                <input type="text" 
                    wire:model.live.debounce.300ms="searchTerm" 
                    class="form-control filter-control" 
                    placeholder="Tìm kiếm theo tên...">
            </div>

            {{-- Filter trạng thái --}}
            <div class="col-6 col-sm-3 col-md-3 col-lg-3">
                <select wire:model.live="filterStatus" class="form-select filter-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </div>

            {{-- Filter ngày --}}
            <div class="col-6 col-sm-3 col-md-3 col-lg-3">
                <input type="date" wire:model.live="filterDate" class="form-control filter-control">
            </div>
        </div>
    </div>
        </div>

        <div class="card clean-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    @if ($feedbackSuccessMessage)
                        <div class="alert alert-success">
                            {{ $feedbackSuccessMessage }}
                        </div>
                    @endif
                    <thead>
                        <tr>
                            <th>Sự Kiện</th>
                            <th>Thời gian</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($schedules as $item)
                            @php
                                $start = \Carbon\Carbon::parse($item->start);
                                $end = \Carbon\Carbon::parse($item->end);
                                $canCancel =
                                    \Carbon\Carbon::now()->addHour()->lt($start) && $item->status !== 'cancelled';
                                $isEnded = $end->isPast();
                                $canFeedback = $isEnded && $item->status !== 'cancelled';

                                $statusLabel =
                                    [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                    ][$item->status] ?? $item->status;
                            @endphp

                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->title }}</div>
                                    @if($item->lab_code)
                                        <div class="text-muted small">{{ $item->lab_code }}</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="small fw-medium">{{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                                    </div>
                                    <div class="text-muted small">{{ $start->format('d/m/Y') }}</div>
                                </td>

                                <td class="text-center">
                                    <span class="status-chip status-{{ $item->status }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">

                                        <button type="button" wire:click="viewSchedule({{ $item->id }})"
                                            class="icon-pill icon-view active-icon" data-bs-toggle="tooltip"
                                            title="Xem chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>

                                       <button type="button" 
                                            wire:click="confirmCancel({{ $item->id }})"
                                            class="icon-pill icon-cancel {{ $canCancel ? 'active-icon' : 'disabled-icon' }}"
                                            @disabled(!$canCancel) 
                                            data-bs-toggle="tooltip"
                                            title="{{ $canCancel ? 'Hủy lịch' : 'Không thể hủy' }}">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>

                                        @php
                                            $canOpenFeedback = $this->canFeedback($item) || filled($item->feedback);

                                            $canSendFeedback = $this->canFeedback($item);

                                            $tooltip = $canSendFeedback
                                                ? 'Gửi phản hồi'
                                                : (filled($item->feedback)
                                                    ? 'Xem phản hồi'
                                                    : 'Chưa thể phản hồi');
                                        @endphp

                                        <button type="button" wire:click="openFeedback({{ $item->id }})"
                                            class="icon-pill icon-feedback {{ $canOpenFeedback ? 'active-icon' : 'disabled-icon' }}"
                                            @disabled(!$canOpenFeedback) data-bs-toggle="tooltip"
                                            title="{{ $tooltip }}">
                                            <i class="bi bi-chat-dots-fill"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">
                                    @if($searchTerm || $filterStatus || $filterDate)
                                        Không tìm thấy lịch trình nào phù hợp.
                                    @else
                                        Chưa có lịch trình nào.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $schedules->links() }}
        </div>

        {{-- Modal xác nhận hủy lịch --}}
        <div wire:ignore.self class="modal fade" id="modalConfirmCancel" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content modal-clean shadow-lg border-0">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="fw-bold text-warning m-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận hủy lịch
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3">
                        <p class="text-muted small mb-0">
                            Bạn có chắc chắn muốn hủy lịch trình này không? Lịch đã hủy sẽ không thể khôi phục.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">
                            Không
                        </button>
                        <button type="button" wire:click="cancelSchedule" class="btn btn-sm btn-warning rounded-pill px-3">
                            <span wire:loading.remove wire:target="cancelSchedule">Đồng ý hủy</span>
                            <span wire:loading wire:target="cancelSchedule" class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal phản hồi --}}
        <div wire:ignore.self class="modal fade" id="feedbackModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-clean">
                    <div class="modal-header border-0">
                        <h6 class="fw-semibold m-0">Gửi phản hồi</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{--  Nhúng form báo hỏng nhiều thiết bị theo event --}}
                        @if ($selectedEventId)
                            <livewire:client.equipment-issues.create-from-event :labEventId="$selectedEventId" :key="'issue-from-event-' . $selectedEventId" />
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>

                        {{-- Nút này gọi sang parent, parent dispatch xuống child --}}
                        @php
                            $locked = filled($selectedEvent?->feedback);
                        @endphp

                        @if ($locked)
                            <button type="button" class="btn btn-secondary" disabled>Đã phản hồi</button>
                        @else
                            <button type="button" class="btn btn-primary"
                                wire:click="$dispatch('submitIssueRequest')">Gửi</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal chi tiết --}}
        <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-clean">
                    <div class="modal-header border-0">
                        <h6 class="fw-semibold m-0">Chi tiết đăng ký</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        @if ($selectedSchedule)
                            <div class="info-box">
                                <label>Tên sự kiện</label>
                                <div>{{ $selectedSchedule->title }}</div>
                            </div>

                            @if($selectedSchedule->lab_code)
                                <div class="info-box">
                                    <label>Mã khóa mở cửa</label>
                                    <div>{{ $selectedSchedule->lab_code }}</div>
                                </div>
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label>Bắt đầu</label>
                                    <div class="small fw-medium">
                                        {{ \Carbon\Carbon::parse($selectedSchedule->start)->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Kết thúc</label>
                                    <div class="small fw-medium">
                                        {{ \Carbon\Carbon::parse($selectedSchedule->end)->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-box">
                                <label>Trạng thái</label>
                                <div>
                                    @php
                                        $statusLabel = [
                                            'pending' => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy',
                                        ][$selectedSchedule->status] ?? $selectedSchedule->status;
                                    @endphp
                                    <span class="status-chip status-{{ $selectedSchedule->status }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-box">
                                <label>Ghi chú</label>
                                <div class="small">
                                    {{ $selectedSchedule->description ?? 'Không có ghi chú.' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Toast notification --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1085;">
        <div id="appToast" class="toast align-items-center text-white bg-dark border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <style>
        .clean-card {
            border: 1px solid #ececec;
            border-radius: 16px;
            background: #fff;
            overflow: hidden
        }

        .table thead th {
            font-size: .75rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            border: none;
            background: #fafafa
        }

        .table tbody tr {
            transition: background .2s
        }

        .table tbody tr:hover {
            background: #fafafa
        }

        .filter-control {
            background: #f7f7f7;
            border-radius: 10px;
            border: 1px solid #e6e6e6;
            font-size: .875rem;
        }

        .filter-control:focus {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .status-chip {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 600;
            border: 1px solid #e6e6e6;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .status-approved {
            color: #15803d;
            background: #ecfdf3;
            border-color: #bbf7d0
        }

        .status-pending {
            color: #b45309;
            background: #fffbeb;
            border-color: #fed7aa
        }

        .status-completed {
            color: #1d4ed8;
            background: #eef2ff;
            border-color: #c7d2fe
        }

        .status-cancelled {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fecaca
        }

        .icon-pill {
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            font-size: 1rem;
            transition: .2s
        }

        .icon-pill i {
            line-height: 1
        }

        .active-icon:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(15, 23, 42, .08)
        }

        .icon-view {
            background: #f3f4f6;
            color: #111
        }

        .icon-view.active-icon {
            background: #eff6ff;
            color: #1d4ed8
        }

        .icon-cancel.active-icon {
            background: #fef2f2;
            color: #b91c1c
        }

        .icon-feedback.active-icon {
            background: #ecfdf3;
            color: #15803d
        }

        .disabled-icon {
            background: #f5f5f5;
            color: #9ca3af;
            cursor: not-allowed;
            filter: grayscale(100%)
        }

        .icon-pill:disabled {
            background: #f5f5f5;
            color: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
            filter: grayscale(100%)
        }

        .icon-pill:disabled:hover {
            transform: none;
            box-shadow: none
        }

        .modal-clean {
            border-radius: 18px;
            border: 1px solid #ececec
        }

        .info-box {
            background: #f9fafb;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px
        }

        .info-box label {
            font-size: .7rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .feedback-input {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            const initTooltips = () => {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    const inst = bootstrap.Tooltip.getInstance(el);
                    if (inst) inst.dispose();
                    new bootstrap.Tooltip(el);
                });
            };

            initTooltips();

            Livewire.on('open-modal', (payload) => {
                const id = typeof payload === 'string' ? payload : payload?.id;
                if (!id) return;

                const modalElement = document.getElementById(id);
                if (!modalElement) return;

                const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.show();
            });

            Livewire.on('close-modal', (payload) => {
                const id = typeof payload === 'string' ? payload : payload?.id;
                if (!id) return;

                const modalElement = document.getElementById(id);
                if (!modalElement) return;

                const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.hide();
            });

            Livewire.on('toaster', (payload) => {
                const msg = typeof payload === 'string' ?
                    payload :
                    (payload?.message ?? '');

                if (!msg) return;

                const toastEl = document.getElementById('appToast');
                const toastMsgEl = document.getElementById('toastMessage');
                if (!toastEl || !toastMsgEl) return;

                toastMsgEl.innerText = msg;

                const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                toast.show();
            });

            Livewire.hook('message.processed', () => {
                initTooltips();
            });

            // Khi preview modal mở => ẩn feedback modal (tránh stack 2 modal)
            document.addEventListener('show.bs.modal', (e) => {
                if (e.target && e.target.id === 'issuePreviewModal') {
                    const fbEl = document.getElementById('feedbackModal');
                    if (!fbEl) return;

                    const fb = bootstrap.Modal.getOrCreateInstance(fbEl);
                    fb.hide();
                }
            });

            // Khi preview modal đóng => mở lại feedback modal
            document.addEventListener('hidden.bs.modal', (e) => {
                if (e.target && e.target.id === 'issuePreviewModal') {
                    const fbEl = document.getElementById('feedbackModal');
                    if (!fbEl) return;

                    const fb = bootstrap.Modal.getOrCreateInstance(fbEl);
                    fb.show();
                }
            });
        });
    </script>
</div>