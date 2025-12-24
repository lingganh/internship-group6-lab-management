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
                            <div class="d-inline-flex gap-2 align-items-center">

                                <button wire:click="viewSchedule({{ $item->id }})" class="icon-btn text-primary" data-bs-toggle="tooltip" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($canCancel)
                                    <button wire:click="cancelSchedule({{ $item->id }})"
                                            onclick="confirm('Hủy lịch này?') || event.stopImmediatePropagation()"
                                            class="icon-btn text-danger"
                                            data-bs-toggle="tooltip"
                                            title="Hủy lịch">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif

                                @if($isEnded && $item->status !== 'cancelled')
                                    <button wire:click="openFeedback({{ $item->id }})"
                                            class="icon-btn text-primary"
                                            data-bs-toggle="tooltip"
                                            title="Phản hồi">
                                        <i class="bi bi-chat-dots"></i>
                                    </button>
                                @endif

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

                    <label class="small fw-semibold mb-1">Góp ý</label>
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

.clean-card{
    border:1px solid #ececec;
    border-radius:16px;
    background:#fff;
}

.table thead th{
    font-size:.75rem;
    color:#6b7280;
    text-transform:uppercase;
    font-weight:600;
    border:none;
    background:#fafafa;
}

.table tbody tr{transition:background .2s;}
.table tbody tr:hover{background:#fafafa;}

.filter-control{
    background:#f7f7f7;
    border-radius:10px;
    border:1px solid #e6e6e6;
}

.status-chip{
    padding:6px 12px;
    border-radius:999px;
    font-size:.7rem;
    font-weight:600;
    border:1px solid #e6e6e6;
}

.status-approved{ color:#16794d;background:#f3fbf7;border-color:#cfe9dd; }
.status-pending{ color:#9b6b00;background:#fff9e8;border-color:#ffe7b0; }
.status-completed{ color:#1d4ed8;background:#eef4ff;border-color:#c7d6ff; }
.status-cancelled{ color:#a61d24;background:#fff1f0;border-color:#ffccc7; }

.icon-btn{
    background:none;
    border:none;
    font-size:1.2rem;
    padding:6px;
    border-radius:8px;
    transition:.2s;
}
.icon-btn:hover{background:#f2f4f5;}

.modal-clean{
    border-radius:18px;
    border:1px solid #ececec;
}

.info-box{
    background:#f9fafb;
    border-radius:10px;
    padding:10px 12px;
    margin-bottom:12px;
}

.info-box label{
    font-size:.7rem;
    text-transform:uppercase;
    color:#6c757d;
    font-weight:600;
}

.feedback-input{
    border-radius:10px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
}

</style>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
})

document.addEventListener('livewire:init', () => {
    Livewire.on('open-modal', (event) => {
        const modalElement = document.getElementById(event.id);
        if (!modalElement) return;
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    Livewire.on('notify', e => {
        document.getElementById('toastMessage').innerText = e.message;
        const toast = new bootstrap.Toast(document.getElementById('appToast'));
        toast.show();
    });
});
</script>

</div>