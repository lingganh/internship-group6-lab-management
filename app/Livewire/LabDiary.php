<?php

namespace App\Livewire;

use App\Models\Lab;
use App\Models\LabEvent;
use Livewire\Component;
use Livewire\WithPagination;

class LabDiary extends Component
{
    use WithPagination;

    public $filterLabCode = '';
    public $filterStatus = '';
    public $filterFrom = '';
    public $filterTo = '';
    public $keyword = '';

    public $selectedEvent = null;

    public $edit = [
        'title' => '',
        'category' => 'work',
        'lab_code' => '',
        'start' => '',
        'end' => '',
        'description' => '',
        'color' => '#3498db',
        'status' => 'pending',
        'user_id' => '',
        'feedback' => '',
    ];

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'filterLabCode' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterFrom' => ['except' => ''],
        'filterTo' => ['except' => ''],
        'keyword' => ['except' => ''],
    ];

    public function categoryLabel(?string $cat): string
    {
        return match ($cat) {
            'work' => 'Làm việc / nghiên cứu',
            'seminar' => 'Hội thảo / seminar',
            'other' => 'Khác',
            default => $cat ?: '—',
        };
    }

    public function updatingFilterLabCode()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterFrom()
    {
        $this->resetPage();
    }
    public function updatingFilterTo()
    {
        $this->resetPage();
    }
    public function updatingKeyword()
    {
        $this->resetPage();
    }

    private function hasConflict(string $labCode, string $start, string $end, ?int $ignoreId = null): bool
    {
        return LabEvent::where('status', 'approved')
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('lab_code', $labCode)
            ->where(function ($q) use ($start, $end) {
                $q->where('start', '<', $end)
                    ->where('end', '>', $start);
            })
            ->exists();
    }

    public function viewEvent($id)
    {
        $this->selectedEvent = LabEvent::with(['user', 'files', 'lab'])->findOrFail($id);

        $this->edit = [
            'title' => (string) ($this->selectedEvent->title ?? ''),
            'category' => (string) ($this->selectedEvent->category ?? 'work'),
            'lab_code' => (string) ($this->selectedEvent->lab_code ?? ''),
            'start' => $this->selectedEvent->start ? $this->selectedEvent->start->format('Y-m-d\TH:i') : '',
            'end' => $this->selectedEvent->end ? $this->selectedEvent->end->format('Y-m-d\TH:i') : '',
            'description' => (string) ($this->selectedEvent->description ?? ''),
            'color' => (string) ($this->selectedEvent->color ?? '#3498db'),
            'status' => (string) ($this->selectedEvent->status ?? 'pending'),
            'user_id' => (string) ($this->selectedEvent->user_id ?? ''),
            'feedback' => (string) ($this->selectedEvent->feedback ?? ''),
        ];

        $this->dispatch('open-details-modal');
    }


    public function openDeleteConfirm()
    {
        if (!$this->selectedEvent)
            return;
        $this->dispatch('open-confirm-modal');
    }
    private function flashToast(string $type, string $message)
    {
        session()->flash($type, $message);
        $this->dispatch('toast', type: $type, message: $message);
    }

    public function updateEvent()
    {
        if (!$this->selectedEvent)
            return;

        $this->validate([
            'edit.title' => 'required|string|max:255',
            'edit.category' => 'required|string|max:50',
            'edit.lab_code' => 'required|string|max:50',
            'edit.start' => 'required|date',
            'edit.end' => 'required|date',
            'edit.description' => 'nullable|string|max:5000',
            'edit.status' => 'required|in:pending,approved,cancelled',
            'edit.feedback' => 'nullable|string|max:2000',
        ]);

        if ($this->edit['status'] === 'approved') {
            if ($this->hasConflict($this->edit['lab_code'], $this->edit['start'], $this->edit['end'], $this->selectedEvent->id)) {
                $this->flashToast('error', 'Khung giờ này đã có lịch được duyệt trong phòng.');
                return;
            }
        }

        $ev = LabEvent::findOrFail($this->selectedEvent->id);

        $ev->update([
            'title' => $this->edit['title'],
            'category' => $this->edit['category'],
            'lab_code' => $this->edit['lab_code'],
            'start' => $this->edit['start'],
            'end' => $this->edit['end'],
            'description' => $this->edit['description'] ?: null,
            'status' => $this->edit['status'],
            'feedback' => trim((string) $this->edit['feedback']) !== '' ? trim((string) $this->edit['feedback']) : null,
        ]);

        $this->selectedEvent = null;

        $this->dispatch('close-details-modal');
        $this->flashToast('success', 'Đã lưu thông tin lịch.');
    }

    public function deleteEvent()
    {
        if (!$this->selectedEvent)
            return;

        $id = $this->selectedEvent->id;

        $ev = LabEvent::find($id);
        if (!$ev) {
            $this->dispatch('close-confirm-modal');
            $this->dispatch('close-details-modal');
            $this->selectedEvent = null;
            $this->flashToast('warning', 'Lịch đã bị xóa trước đó.');
            return;
        }

        $ev->delete();

        $this->selectedEvent = null;

        $this->dispatch('close-confirm-modal');
        $this->dispatch('close-details-modal');

        $this->flashToast('success', "Đã xóa lịch #{$id}.");
    }


    public function render()
    {
        $labs = Lab::select('code', 'name')->orderBy('name')->get();

        $q = LabEvent::query()
            ->with(['user:id,full_name,email', 'lab:code,name'])
            ->orderByDesc('start');

        if ($this->filterLabCode !== '')
            $q->where('lab_code', $this->filterLabCode);
        if ($this->filterStatus !== '')
            $q->where('status', $this->filterStatus);
        if ($this->filterFrom !== '')
            $q->whereDate('start', '>=', $this->filterFrom);
        if ($this->filterTo !== '')
            $q->whereDate('start', '<=', $this->filterTo);

        if (trim($this->keyword) !== '') {
            $kw = trim($this->keyword);
            $q->where(function ($sub) use ($kw) {
                $sub->where('title', 'like', "%{$kw}%")
                    ->orWhere('category', 'like', "%{$kw}%")
                    ->orWhere('description', 'like', "%{$kw}%")
                    ->orWhere('feedback', 'like', "%{$kw}%");
            });
        }

        $events = $q->paginate(15);

        return view('livewire.lab-diary', compact('labs', 'events'))
            ->layout('components.layouts.admin-layout');
    }
}
