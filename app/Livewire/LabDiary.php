<?php

namespace App\Livewire;

use App\Models\Lab;
use App\Models\LabEvent;
use App\Models\LabEventFile;
use App\Models\User;
use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class LabDiary extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $filterLabCode = '';
    public $filterStatus = '';
    public $filterFrom = '';
    public $filterTo = '';
    public $keyword = '';
    public $ExportData=null;

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
        'group_id' => '',
    ];

    // File mới upload trong modal
    public $newFiles = [];

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

    public function removeNewFile($index)
    {
        if (isset($this->newFiles[$index])) {
            unset($this->newFiles[$index]);
            $this->newFiles = array_values($this->newFiles); // reset lại key 0,1,2,...
        }
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
        $this->selectedEvent = LabEvent::with(['user', 'files', 'lab', 'group'])->findOrFail($id);

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
            'group_id' => (string) ($this->selectedEvent->group_id ?? $this->selectedEvent->registered_for ?? ''),
            'feedback' => (string) ($this->selectedEvent->feedback ?? ''),
        ];

        $this->newFiles = [];

        $this->dispatch('open-details-modal');
    }

    public function openDeleteConfirm()
    {
        if (!$this->selectedEvent) {
            return;
        }
        $this->dispatch('open-confirm-modal');
    }

    private function flashToast(string $type, string $message)
    {
        session()->flash($type, $message);
        $this->dispatch('toast', type: $type, message: $message);
    }

    public function updateEvent()
    {
        if (!$this->selectedEvent) {
            return;
        }

        $this->validate([
            'edit.title' => 'required|string|max:255',
            'edit.category' => 'required|string|max:50',
            'edit.lab_code' => 'required|string|max:50',
            'edit.start' => 'required|date',
            'edit.end' => 'required|date',
            'edit.description' => 'nullable|string|max:5000',
            'edit.status' => 'required|in:pending,approved,cancelled,completed',
            'edit.feedback' => 'nullable|string|max:2000',
            'edit.user_id' => 'nullable|exists:users,id',
            'edit.group_id' => 'nullable|exists:groups,id',
            'newFiles.*' => 'nullable|file|max:5120', // 5MB/file
        ]);

        if (in_array($this->edit['status'], ['approved', 'completed'])) {
            if (
                $this->hasConflict(
                    $this->edit['lab_code'],
                    $this->edit['start'],
                    $this->edit['end'],
                    $this->selectedEvent->id
                )
            ) {
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
            'user_id' => $this->edit['user_id'] ?: null,
            'registered_for' => $this->edit['group_id'] ?: null,
            'feedback' => trim((string) $this->edit['feedback']) !== '' ? trim((string) $this->edit['feedback']) : null,
        ]);

        // Lưu file mới (nếu có)
        if (!empty($this->newFiles)) {
            foreach ($this->newFiles as $file) {
                try {
                    $path = $file->store('lab_files', 'public');

                    LabEventFile::create([
                        'lab_event_id' => $ev->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Diary upload error: ' . $e->getMessage());
                }
            }
        }

        $this->newFiles = [];
        $this->selectedEvent = null;

        $this->dispatch('close-details-modal');
        $this->flashToast('success', 'Đã lưu thông tin lịch.');
    }

    public function deleteEvent()
    {
        if (!$this->selectedEvent) {
            return;
        }

        $id = $this->selectedEvent->id;

        $ev = LabEvent::find($id);
        if (!$ev) {
            $this->dispatch('close-confirm-modal');
            $this->dispatch('close-details-modal');
            $this->selectedEvent = null;
            $this->flashToast('warning', 'Lịch đã bị xóa trước đó.');
            return;
        }

        // Xóa file đính kèm
        $files = LabEventFile::where('lab_event_id', $id)->get();
        foreach ($files as $file) {
            try {
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
            } catch (\Throwable $e) {
                Log::error('Delete diary file error: ' . $e->getMessage());
            }
            $file->delete();
        }

        $ev->delete();

        $this->selectedEvent = null;

        $this->dispatch('close-confirm-modal');
        $this->dispatch('close-details-modal');

        $this->flashToast('success', "Đã xóa lịch #{$id}.");
    }

    /**
     * Xóa 1 file cũ trong modal chi tiết
     */
    public function deleteFile(int $fileId)
    {
        if (!$this->selectedEvent) {
            return;
        }

        $file = LabEventFile::where('lab_event_id', $this->selectedEvent->id)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            $this->flashToast('warning', 'File đã bị xóa trước đó.');
            return;
        }

        try {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();

            $this->selectedEvent = $this->selectedEvent->fresh('files');

            $this->flashToast('success', 'Đã xóa file đính kèm.');
        } catch (\Throwable $e) {
            Log::error('Delete diary file error: ' . $e->getMessage());
            $this->flashToast('error', 'Xóa file thất bại, vui lòng thử lại.');
        }
    }

    public function render()
    {
        $labs = Lab::select('code', 'name')->orderBy('name')->get();

        $users = User::select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        $groups = Group::select('id', 'name')
            ->orderBy('name')
            ->get();

        $q = LabEvent::query()
            ->with([
                'user:id,full_name,email',
                'lab:code,name',
                'group:id,name',
                'files',
            ])
            ->where('status', 'completed')
            ->where('end', '<', now())
            ->orderByDesc('start');

        if ($this->filterLabCode !== '') {
            $q->where('lab_code', $this->filterLabCode);
        }
        if ($this->filterStatus !== '') {
            $q->where('status', $this->filterStatus);
        }
        if ($this->filterFrom !== '') {
            $q->whereDate('start', '>=', $this->filterFrom);
        }
        if ($this->filterTo !== '') {
            $q->whereDate('start', '<=', $this->filterTo);
        }

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

        $this->ExportData=$q->get();
       
        return view('livewire.lab-diary', compact('labs', 'events', 'users', 'groups'))
            ->layout('components.layouts.admin-layout');
    }

   

public function export()
{
    $events = $this->ExportData;

    
    Cache::put(
        'lab-diary-export-' . auth()->id(),
        $events,
        now()->addMinutes(5)
    );

    return redirect()->route('lab-diary.export');
}
}
