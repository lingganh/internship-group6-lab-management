<?php

namespace App\Livewire;

use App\Models\LabEvent;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Approval extends Component
{
    use WithPagination;

    public $filterStatus = 'pending';
    public $filterUserId = '';
    public $filterDate = '';

    public $selectedSchedule = null;

    public $confirmType = '';
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $confirmId = null;
    public $confirmNote = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'filterStatus' => ['except' => 'pending'],
        'filterUserId' => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterUserId() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }

    public function viewSchedule($scheduleId)
    {
        $this->selectedSchedule = LabEvent::with(['user', 'files'])->findOrFail($scheduleId);
        $this->dispatch('open-details-modal');
    }

    public function confirmReject($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) return;

        $this->confirmType = 'reject';
        $this->confirmTitle = 'Xác nhận từ chối';
        $this->confirmMessage = 'Bạn chắc chắn muốn từ chối lịch đăng ký này?';
        $this->confirmId = $id;
        $this->confirmNote = '';

        $this->dispatch('open-confirm-modal');
    }

    public function confirmApprove($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) return;

        $this->confirmType = 'approve';
        $this->confirmTitle = 'Xác nhận phê duyệt';
        $this->confirmMessage = 'Bạn chắc chắn muốn phê duyệt lịch đăng ký này?';
        $this->confirmId = $id;
        $this->confirmNote = '';

        $this->dispatch('open-confirm-modal');
    }

    public function performConfirm()
    {
        if (!$this->confirmId || !$this->confirmType) {
            $this->dispatch('toast', type: 'error', message: 'Không hợp lệ', sub: 'Thiếu dữ liệu xác nhận.');
            return;
        }

        if ($this->confirmType === 'reject') {
            $this->rejectSchedule($this->confirmId);
        }

        if ($this->confirmType === 'approve') {
            $this->approveSchedule($this->confirmId);
        }

        $this->dispatch('close-confirm-modal');

        $this->confirmType = '';
        $this->confirmId = null;
        $this->confirmNote = '';
    }

    public function approveSchedule($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) return;

        $schedule = LabEvent::findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch('toast', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $hasConflict = LabEvent::where('status', 'approved')
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($schedule) {
                $query->where('start', '<', $schedule->end)
                      ->where('end', '>', $schedule->start);
            })
            ->exists();

        if ($hasConflict) {
            $this->dispatch('toast', type: 'error', message: 'Trùng lịch', sub: 'Trùng khung giờ với một lịch khác đã duyệt.');
            return;
        }

        $schedule->update(['status' => 'approved']);

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        $this->dispatch('toast', type: 'success', message: 'Đã phê duyệt', sub: 'Yêu cầu đã được cập nhật.');
        $this->dispatch('close-details-modal');
    }

    public function rejectSchedule($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) return;

        $schedule = LabEvent::findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch('toast', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $schedule->update(['status' => 'cancelled']);

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        $this->dispatch('toast', type: 'success', message: 'Đã từ chối', sub: 'Yêu cầu đã được cập nhật.');
        $this->dispatch('close-details-modal');
    }

    public function render()
    {
        $schedules = LabEvent::with(['user'])
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUserId !== '', fn($q) => $q->where('user_id', $this->filterUserId))
            ->when($this->filterDate !== '', fn($q) => $q->whereDate('start', $this->filterDate))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.approval', [
            'schedules' => $schedules,
            'pendingCount' => LabEvent::where('status', 'pending')->count(),
            'users' => User::select('id', 'full_name')->orderBy('full_name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}
