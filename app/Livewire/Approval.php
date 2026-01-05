<?php

namespace App\Livewire;

use App\Models\LabEvent;
use App\Models\User;
use App\Models\Lab;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'filterStatus' => ['except' => 'pending'],
        'filterUserId' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterLabCode' => ['except' => ''],
    ];

    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterUserId() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }
    public function updatingFilterLabCode() { $this->resetPage(); }

    public function viewSchedule($scheduleId)
    {
        $this->selectedSchedule = LabEvent::with(['user', 'files', 'lab'])->findOrFail($scheduleId);
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

    public function performConfirm()
    {
        if (!$this->confirmId || !$this->confirmType) {
            $this->dispatch('toast', type: 'error', message: 'Không hợp lệ', sub: 'Thiếu dữ liệu xác nhận.');
            return;
        }

        if ($this->confirmType === 'reject') {
            $this->rejectSchedule($this->confirmId);
        }

        $this->dispatch('close-confirm-modal');

        $this->confirmType = '';
        $this->confirmId = null;
        $this->confirmNote = '';
    }

    // PHÊ DUYỆT THẲNG (KHÔNG CONFIRM)
    public function approveNow($id = null)
    {
        $this->approveSchedule($id);
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

        // Check trùng lịch theo CÙNG PHÒNG
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
