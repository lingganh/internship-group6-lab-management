<?php
namespace App\Livewire;

use App\Models\LabEvent;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserSchedules extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterDate = '';
    public $selectedSchedule; // Lưu dữ liệu để hiện lên Modal
    
    public $rating = 5;
    public $comment = '';

    protected $paginationTheme = 'bootstrap';

     public function viewSchedule($id)
    {
        $this->selectedSchedule = LabEvent::where('user_id', Auth::id())->findOrFail($id);
        $this->dispatch('show-detail-modal');  
    }

     public function openFeedback($id)
    {
        $this->selectedSchedule = LabEvent::findOrFail($id);
        $this->dispatch('show-feedback-modal');
    }

    // Logic Hủy lịch
    public function cancelSchedule($id)
    {
        $event = LabEvent::where('user_id', Auth::id())->findOrFail($id);

         if (Carbon::now()->addHour()->gt(Carbon::parse($event->start))) {
            session()->flash('error', 'Chỉ có thể hủy lịch trước khi bắt đầu ít nhất 1 giờ!');
            return;
        }

        $event->update(['status' => 'cancelled']);
        session()->flash('message', 'Đã hủy lịch thành công.');
    }

    public function render()
    {
        $schedules = LabEvent::where('user_id', Auth::id())
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDate, fn($q) => $q->whereDate('start', $this->filterDate))
            ->orderBy('start', 'desc')
            ->paginate(10);

        return view('livewire.user-schedules', [
            'schedules' => $schedules
        ])->layout('components.layouts.client-layout');
    }
}