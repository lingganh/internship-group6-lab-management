<?php

namespace App\Livewire;

use App\Models\LabEvent;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Approval extends Component
{
    use WithPagination;

    public $filterStatus = 'pending';
    public $filterUserId = '';
    public $filterDate = '';
    
    public $selectedSchedule;
    public $showDetails = false;

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
        $this->showDetails = true;
    }

    public function approveSchedule()
    {
        if (!$this->selectedSchedule || $this->selectedSchedule->status!== 'pending') {
            session()->flash('error', 'Lịch này không thể xử lý.');
            return;
        }

        // Kiểm tra trùng lịch 
        $hasConflict = LabEvent::where('status', 'approved')
            ->where('id', '!=', $this->selectedSchedule->id)
            ->where(function($query) {
                $query->where('start', '<', $this->selectedSchedule->end)
                      ->where('end', '>', $this->selectedSchedule->start);
            })->exists();

        if ($hasConflict) {
            session()->flash('error', 'Trùng khung giờ với một lịch khác đã duyệt!');
            return;
        }

        $this->selectedSchedule->update(['status' => 'approved']);
        session()->flash('success', 'Đã phê duyệt thành công.');
        $this->dispatch('close-modal');
    }

    public function rejectSchedule($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) return;

        $schedule = LabEvent::findOrFail($id);
        $schedule->update(['status' => 'rejected']);  
        
        session()->flash('success', 'Đã từ chối lịch.');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $schedules = LabEvent::with(['user'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUserId, fn($q) => $q->where('user_id', $this->filterUserId))
            ->when($this->filterDate, fn($q) => $q->whereDate('start', $this->filterDate))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.approval', [
            'schedules' => $schedules,
            'pendingCount' => LabEvent::where('status', 'pending')->count(),
            'users' => User::all(),
        ])->layout('components.layouts.admin-layout');
    }
}