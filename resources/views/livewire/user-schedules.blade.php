<div>
    <div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h5 class="fw-semibold text-dark m-0">Lịch trình cá nhân</h5>

        <div class="d-flex gap-2">
            <select wire:model.live="filterStatus" class="form-select form-select-sm filter-control">
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ duyệt</option>
                <option value="approved">Đã duyệt</option>
                <option value="completed">Hoàn thành</option>
            </select>

            <input type="date" wire:model.live="filterDate" class="form-control form-control-sm filter-control">
        </div>
    </div>

    <div class="card clean-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Nội dung</th>
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
                        $canCancel = \Carbon\Carbon::now()->addHour()->lt($start) && $item->status !== 'cancelled';
                        $isEnded = $end->isPast();
                        $statusLabel = [
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ][$item->status] ?? $item->status;
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->title }}</div>
                            <div class="text-muted small">Phòng máy trung tâm</div>
                        </td>

                        <td>
                            <div class="small fw-medium">{{ $start->format('H:i') }} - {{ $end->format('H:i') }}</div>
                            <div class="text-muted small">{{ $start->format('d/m/Y') }}</div>
                        </td>

                        <td class="text-center">
                            <span class="status-chip status-{{ $item->status }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center">

                                 <button
                                    wire:click="viewSchedule({{ $item->id }})"
                                    class="icon-pill icon-view active-icon"
                                    data-bs-toggle="tooltip"
                                    title="Xem chi tiết">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                 <button
                                    wire:click="cancelSchedule({{ $item->id }})"
                                    class="icon-pill icon-cancel {{ $canCancel ? 'active-icon' : 'disabled-icon' }}"
                                    data-bs-toggle="tooltip"
                                    title="{{ $canCancel ? 'Hủy lịch' : 'Không thể hủy (chỉ hủy trước giờ bắt đầu tối thiểu 1 giờ)' }}">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>

                                 <button
                                    wire:click="openFeedback({{ $item->id }})"
                                    class="icon-pill icon-feedback {{ ($isEnded && $item->status !== 'cancelled') ? 'active-icon' : 'disabled-icon' }}"
                                    data-bs-toggle="tooltip"
                                    title="{{ ($isEnded && $item->status !== 'cancelled') ? 'Gửi phản hồi' : 'Chưa thể phản hồi' }}">
                                    <i class="bi bi-chat-dots-fill"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small">
                            Chưa có lịch trình nào.
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

    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-clean">
                <div class="modal-header border-0">
                    <h6 class="fw-semibold m-0">Chi tiết đăng ký</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if($selectedSchedule)
                        <div class="info-box">
                            <label>Tên sự kiện</label>
                            <div>{{ $selectedSchedule->title }}</div>
                        </div>

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

    <div wire:ignore.self class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-clean">
                <div class="modal-header border-0">
                    <h6 class="fw-semibold m-0">Gửi phản hồi</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="small fw-semibold mb-1">Chi tiết góp ý</label>
                    <textarea wire:model="comment" rows="4" class="form-control feedback-input" placeholder="Nhập ý kiến của bạn..."></textarea>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button wire:click="submitFeedback" class="btn btn-primary rounded-pill px-4">Gửi</button>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="appToast" class="toast align-items-center text-white bg-dark border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
.clean-card{border:1px solid #ececec;border-radius:16px;background:#fff}
.table thead th{font-size:.75rem;color:#6b7280;text-transform:uppercase;font-weight:600;border:none;background:#fafafa}
.table tbody tr{transition:background .2s}
.table tbody tr:hover{background:#fafafa}
.filter-control{background:#f7f7f7;border-radius:10px;border:1px solid #e6e6e6}

.status-chip{padding:6px 12px;border-radius:999px;font-size:.7rem;font-weight:600;border:1px solid #e6e6e6}
.status-approved{color:#15803d;background:#ecfdf3;border-color:#bbf7d0}
.status-pending{color:#b45309;background:#fffbeb;border-color:#fed7aa}
.status-completed{color:#1d4ed8;background:#eef2ff;border-color:#c7d2fe}
.status-cancelled{color:#b91c1c;background:#fef2f2;border-color:#fecaca}

.icon-pill{border:none;display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:999px;font-size:1rem;transition:.2s}
.icon-pill i{line-height:1}
.active-icon:hover{transform:translateY(-1px);box-shadow:0 2px 6px rgba(15,23,42,.08)}

.icon-view{background:#f3f4f6;color:#111}
.icon-view.active-icon{background:#eff6ff;color:#1d4ed8}
.icon-cancel.active-icon{background:#fef2f2;color:#b91c1c}
.icon-feedback.active-icon{background:#ecfdf3;color:#15803d}

.disabled-icon{
    background:#f5f5f5;
    color:#9ca3af;
    cursor:not-allowed;
    filter:grayscale(100%);
 }

.modal-clean{border-radius:18px;border:1px solid #ececec}
.info-box{background:#f9fafb;border-radius:10px;padding:10px 12px;margin-bottom:12px}
.info-box label{font-size:.7rem;text-transform:uppercase;color:#6c757d;font-weight:600}
.feedback-input{border-radius:10px;border:1px solid #e5e7eb;background:#f9fafb}
</style>

<script>
document.addEventListener('livewire:init', () => {
    console.log('[UserSchedules] livewire:init');

    const initTooltips = () => {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    };

     initTooltips();

    Livewire.on('open-modal', (payload) => {
        console.log('[UserSchedules] open-modal', payload);

        const id = typeof payload === 'string' ? payload : payload?.id;
        if (!id) return;

        const modalElement = document.getElementById(id);
        if (!modalElement) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    });

    Livewire.on('close-modal', (payload) => {
        console.log('[UserSchedules] close-modal', payload);

        const id = typeof payload === 'string' ? payload : payload?.id;
        if (!id) return;

        const modalElement = document.getElementById(id);
        if (!modalElement) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.hide();
    });

    Livewire.on('toaster', (payload) => {
        console.log('[UserSchedules] toaster', payload);

        const msg = typeof payload === 'string'
            ? payload
            : (payload?.message ?? '');

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
});
</script>

</div>
