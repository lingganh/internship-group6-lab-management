<?php

namespace App\Livewire;

use App\Models\LabEvent;
use App\Models\User;
use App\Models\Lab;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;

class Approval extends Component
{
    use WithPagination;

    public $filterStatus = 'pending';
    public $filterUserId = '';
    public $filterDate = '';
    public $filterLabCode = '';

    public $selectedSchedule = null;

    public $confirmType = '';
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $confirmId = null;
    public $confirmNote = '';

    public $passwordModalId = null;
    public $roomPassword = '';

    public $rejectionNote = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'filterStatus' => ['except' => 'pending'],
        'filterUserId' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterLabCode' => ['except' => ''],
    ];

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterUserId()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function updatingFilterLabCode()
    {
        $this->resetPage();
    }

    public function viewSchedule($scheduleId)
    {
        $this->selectedSchedule = LabEvent::with(['user', 'files', 'lab', 'group'])->findOrFail($scheduleId);
        $this->dispatch('open-details-modal');
    }

    public function confirmReject($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) {
            return;
        }

        $this->confirmType = 'reject';
        $this->confirmTitle = 'Xác nhận từ chối';
        $this->confirmMessage = 'Bạn chắc chắn muốn từ chối lịch đăng ký này?';
        $this->confirmId = $id;
        $this->confirmNote = '';
        $this->rejectionNote = '';

        $this->dispatch('open-confirm-modal');
    }

    public function performConfirm()
    {
        if (!$this->confirmId || !$this->confirmType) {
            $this->dispatch('toast', type: 'error', message: 'Không hợp lệ', sub: 'Thiếu dữ liệu xác nhận.');
            return;
        }

       

        $this->dispatch('close-confirm-modal');

        $this->confirmType = '';
        $this->confirmId = null;
        $this->confirmNote = '';
        $this->rejectionNote = '';
    }

    public function approveNow($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) {
            return;
        }

        $schedule = LabEvent::findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch('toast', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $hasConflict = LabEvent::where('status', 'approved')
            ->where('id', '!=', $schedule->id)
            ->where('lab_code', $schedule->lab_code)
            ->where(function ($query) use ($schedule) {
                $query->where('start', '<', $schedule->end)
                    ->where('end', '>', $schedule->start);
            })
            ->exists();

        if ($hasConflict) {
            $this->dispatch('toast', type: 'error', message: 'Trùng lịch', sub: 'Trùng khung giờ với một lịch khác đã duyệt (cùng phòng).');
            return;
        }

        $this->passwordModalId = $id;
        $this->roomPassword = '';
        $this->dispatch('open-password-modal');
    }

    public function approveSchedule()
    {
        if (!$this->passwordModalId) {
            $this->dispatch('toast', type: 'error', message: 'Lỗi', sub: 'Không tìm thấy lịch cần phê duyệt.');
            return;
        }

        if (empty($this->roomPassword)) {
            $this->dispatch('toast', type: 'warning', message: 'Thiếu mật khẩu', sub: 'Vui lòng nhập mật khẩu phòng.');
            return;
        }

        $schedule = LabEvent::with(['user', 'lab'])->findOrFail($this->passwordModalId);

        $schedule->update(['status' => 'approved']);

        Mail::to($schedule->user->email)->queue(new \App\Mail\ApprovalNotification($schedule, $this->roomPassword));

        $this->dispatch('close-password-modal');
        $this->passwordModalId = null;
        $this->roomPassword = '';

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        $this->dispatch('toast', type: 'success', message: 'Đã phê duyệt', sub: 'Email đã được gửi đến người dùng.');
        $this->dispatch('close-details-modal');
    }

    public function rejectSchedule($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) {
            return;
        }

        $schedule = LabEvent::with('user')->findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch('toast', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $schedule->update([
            'status' => 'cancelled',
        ]);

        if ($schedule->user && $schedule->user->email) {
            Mail::to($schedule->user->email)->queue(new \App\Mail\RejectionNotification($schedule, $this->rejectionNote));
        }

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        $this->dispatch('toast', type: 'success', message: 'Đã từ chối', sub: 'Yêu cầu đã được cập nhật.');
        $this->dispatch('close-details-modal');
    }

    public function render()
    {
        $schedules = LabEvent::with(['user', 'lab'])
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUserId !== '', fn($q) => $q->where('user_id', $this->filterUserId))
            ->when($this->filterDate !== '', fn($q) => $q->whereDate('start', $this->filterDate))
            ->when($this->filterLabCode !== '', fn($q) => $q->where('lab_code', $this->filterLabCode))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.approval', [
            'schedules' => $schedules,
            'pendingCount' => LabEvent::where('status', 'pending')->count(),
            'users' => User::select('id', 'full_name')->orderBy('full_name')->get(),
            'labs' => Lab::select('code', 'name')->orderBy('name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}
