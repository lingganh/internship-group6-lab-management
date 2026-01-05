<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LabEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserSchedules extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterDate = '';
    public $selectedSchedule = null;
    public $comment = '';

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        // không cần gì ở đây
    ];

    public function render()
    {
        $query = LabEvent::query()
            ->with('user')
            ->whereNot('status', 'cancelled');

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDate !== '') {
            $query->whereDate('start', $this->filterDate);
        }

        $schedules = $query->orderBy('start', 'desc')->paginate(10);

        return view('livewire.user-schedules', [
            'schedules' => $schedules
        ])->layout('components.layouts.client-layout');
    }

    protected function canCancel(LabEvent $event): bool
    {
        $start = Carbon::parse($event->start);
        return Carbon::now()->addHour()->lt($start) && $event->status !== 'cancelled';
    }

    protected function canFeedback(LabEvent $event): bool
    {
        $end = Carbon::parse($event->end);
        return $end->isPast() && $event->status !== 'cancelled';
    }

    public function viewSchedule($id): void
    {
        $event = LabEvent::find($id);

        if (! $event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        $this->selectedSchedule = $event;

        // mở modal detail
        $this->dispatch('open-modal', id: 'detailModal');
    }

    public function openFeedback($id): void
    {
        $event = LabEvent::find($id);

        if (! $event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        if (! $this->canFeedback($event)) {
            $this->dispatch('toaster', 'Chưa thể phản hồi');
            return;
        }

        $this->selectedSchedule = $event;
        $this->comment = '';

        $this->dispatch('open-modal', id: 'feedbackModal');
    }

    public function cancelSchedule($id): void
    {
        $event = LabEvent::find($id);

        if (! $event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        if (! $this->canCancel($event)) {
            $this->dispatch('toaster', 'Không thể hủy (chỉ hủy trước giờ bắt đầu tối thiểu 1 giờ)');
            return;
        }

        $event->status = 'cancelled';
        $event->updated_by = Auth::id();
        $event->updated_at = now();
        $event->save();

        $this->dispatch('toaster', 'Đã hủy lịch');

        $this->reset('selectedSchedule');
        $this->dispatch('$refresh');
    }

    public function submitFeedback(): void
    {
        if (! $this->selectedSchedule) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        $event = LabEvent::find($this->selectedSchedule->id);

        if (! $event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        if (! $this->canFeedback($event)) {
            $this->dispatch('toaster', 'Chưa thể phản hồi');
            return;
        }

        $this->validate([
            'comment' => 'required|string|min:3'
        ], [], [
            'comment' => 'phản hồi'
        ]);

        // $event->feedback = $this->comment;
        // $event->feedback_user_id = Auth::id();
        // $event->feedback_at = now();
        // $event->save();

        $this->dispatch('toaster', 'Đã gửi phản hồi');

        $this->dispatch('close-modal', id: 'feedbackModal');
        $this->reset('comment');
    }
}
