<?php

namespace App\Livewire;

use App\Models\LabEvent;
use App\Models\User;
use App\Models\Lab;
use App\Models\Group;
use App\Models\LabEventFile;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Approval extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $filterStatus = '';
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
    public $roomCode = '';
    public $rejectionNote = '';

    public $conflictId = null;
    public $conflictSchedule = null;

    protected $paginationTheme = 'bootstrap';

    // Thêm các thuộc tính để chỉnh sửa
    public $edit = [
        'title' => '',
        'category' => 'work',
        'lab_code' => '',
        'start' => '',
        'end' => '',
        'description' => '',
        'status' => 'pending',
        'user_id' => '',
        'group_id' => '',
        'feedback' => '',
    ];

    public $newFiles = [];
    
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
            $this->newFiles = array_values($this->newFiles);
        }
    }

    public function viewSchedule($scheduleId)
    {
        $this->selectedSchedule = LabEvent::with(['user', 'files', 'lab', 'group'])->findOrFail($scheduleId);
        
        // Khởi tạo dữ liệu edit
        $this->edit = [
            'title' => (string) ($this->selectedSchedule->title ?? ''),
            'category' => (string) ($this->selectedSchedule->category ?? 'work'),
            'lab_code' => (string) ($this->selectedSchedule->lab_code ?? ''),
            'start' => $this->selectedSchedule->start ? $this->selectedSchedule->start->format('Y-m-d\TH:i') : '',
            'end' => $this->selectedSchedule->end ? $this->selectedSchedule->end->format('Y-m-d\TH:i') : '',
            'description' => (string) ($this->selectedSchedule->description ?? ''),
            'status' => (string) ($this->selectedSchedule->status ?? 'pending'),
            'user_id' => (string) ($this->selectedSchedule->user_id ?? ''),
            'group_id' => (string) ($this->selectedSchedule->group_id ?? $this->selectedSchedule->registered_for ?? ''),
            'feedback' => (string) ($this->selectedSchedule->feedback ?? ''),
        ];
        
        $this->newFiles = [];
        
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

    public function confirmDelete($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) {
            return;
        }

        $this->confirmType = 'delete';
        $this->confirmTitle = 'Xác nhận xóa lịch';
        $this->confirmMessage = 'Hành động này sẽ xóa lịch đăng ký khỏi hệ thống. Bạn chắc chắn?';
        $this->confirmId = $id;
        $this->confirmNote = '';
        $this->rejectionNote = '';

        $this->dispatch('open-confirm-modal');
    }

    public function performConfirm()
    {
        if (!$this->confirmId || !$this->confirmType) {
            $this->dispatch('alert', type: 'error', message: 'Không hợp lệ', sub: 'Thiếu dữ liệu xác nhận.');
            return;
        }

        if ($this->confirmType === 'reject') {
            if (trim($this->rejectionNote) === '') {
                $this->dispatch('alert', type: 'warning', message: 'Thiếu lý do', sub: 'Vui lòng nhập lý do từ chối.');
                return;
            }

            $this->rejectSchedule($this->confirmId);
        } elseif ($this->confirmType === 'delete') {
            $this->deleteSchedule($this->confirmId);
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

        $schedule = LabEvent::with(['lab', 'user'])->findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch('alert', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $conflict = LabEvent::with(['lab', 'user'])
            ->where('status', 'approved')
            ->where('id', '!=', $schedule->id)
            ->where('lab_code', $schedule->lab_code)
            ->where(function ($query) use ($schedule) {
                $query->where('start', '<', $schedule->end)
                    ->where('end', '>', $schedule->start);
            })
            ->orderBy('start')
            ->first();

        if ($conflict) {
            $this->conflictId = $schedule->id;
            $this->conflictSchedule = $conflict;
            $this->dispatch('open-conflict-modal');
            return;
        }

        $this->passwordModalId = $id;
        $this->roomCode = '';
        $this->dispatch('open-password-modal');
    }

    public function forceApprove()
    {
        if (!$this->conflictId) {
            return;
        }

        $schedule = LabEvent::with(['lab', 'user'])->findOrFail($this->conflictId);

        if ($schedule->status !== 'pending') {
            $this->dispatch('alert', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            $this->conflictId = null;
            $this->conflictSchedule = null;
            $this->dispatch('close-conflict-modal');
            return;
        }

        $this->passwordModalId = $schedule->id;
        $this->roomCode = '';
        $this->dispatch('close-conflict-modal');
        $this->dispatch('open-password-modal');
    }

    public function approveSchedule()
    {
        if (!$this->passwordModalId) {
            $this->dispatch('alert', type: 'error', message: 'Lỗi', sub: 'Không tìm thấy lịch cần phê duyệt.');
            return;
        }

        if (empty($this->roomCode)) {
            $this->dispatch('alert', type: 'warning', message: 'Thiếu mã phòng', sub: 'Vui lòng nhập mã phòng lab.');
            return;
        }

        $schedule = LabEvent::with(['user', 'lab'])->findOrFail($this->passwordModalId);

        $schedule->update(['status' => 'approved']);
        $this->notifyUserEventResult($schedule, 'approved');
        if ($schedule->user && $schedule->user->email) {
            Mail::to($schedule->user->email)->queue(
                new \App\Mail\ApprovalNotification($schedule, $this->roomCode)
            );
        }

        $this->dispatch('close-password-modal');
        $this->passwordModalId = null;
        $this->roomCode = '';

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        if ($this->conflictId === $schedule->id) {
            $this->conflictId = null;
            $this->conflictSchedule = null;
            $this->dispatch('close-conflict-modal');
        }

        $this->dispatch('alert', type: 'success', message: 'Đã phê duyệt', sub: 'Email đã được gửi đến người dùng.');
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
            $this->dispatch('alert', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            return;
        }

        $reason = trim($this->rejectionNote) ?: null;

        $schedule->update([
            'status' => 'cancelled',
            'reason' => $reason,
        ]);
        $this->notifyUserEventResult($schedule, 'rejected');
        if ($schedule->user && $schedule->user->email) {
            Mail::to($schedule->user->email)->queue(
                new \App\Mail\RejectionNotification($schedule, $this->rejectionNote)
            );
        }

        if ($this->selectedSchedule && $this->selectedSchedule->id == $schedule->id) {
            $this->selectedSchedule->refresh();
        }

        $this->dispatch('alert', type: 'success', message: 'Đã từ chối', sub: 'Yêu cầu đã được cập nhật.');
        $this->dispatch('close-details-modal');
    }

    public function updateEvent()
    {
        if (!$this->selectedSchedule) {
            return;
        }

        $this->validate([
            'edit.title' => 'required|string|max:255',
            'edit.category' => 'required|string|max:50',
            'edit.lab_code' => 'required|string|max:50',
            'edit.start' => 'required|date',
            'edit.end' => 'required|date|after:edit.start',
            'edit.description' => 'nullable|string|max:5000',
            'edit.status' => 'required|in:pending,approved,cancelled,completed',
            'edit.feedback' => 'nullable|string|max:2000',
            'edit.user_id' => 'nullable|exists:users,id',
            'edit.group_id' => 'nullable|exists:groups,id',
            'newFiles.*' => 'nullable|file|max:5120',
        ], [
            'edit.title.required' => 'Tiêu đề không được để trống.',
            'edit.end.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $ev = LabEvent::findOrFail($this->selectedSchedule->id);

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

        // Lưu file mới
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
                    Log::error('Approval upload error: ' . $e->getMessage());
                }
            }
        }

        $this->newFiles = [];
        $this->selectedSchedule->refresh();

        $this->dispatch('alert', type: 'success', message: 'Đã lưu thông tin lịch.');
    }

    public function deleteSchedule($id = null)
    {
        $id = $id ?? $this->selectedSchedule?->id;
        if (!$id) {
            return;
        }

        $schedule = LabEvent::find($id);

        if (!$schedule) {
            $this->dispatch('alert', type: 'warning', message: 'Không tìm thấy lịch', sub: 'Lịch đã bị xóa trước đó.');
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
                Log::error('Delete approval file error: ' . $e->getMessage());
            }
            $file->delete();
        }

        $schedule->delete();

        if ($this->selectedSchedule && $this->selectedSchedule->id == $id) {
            $this->selectedSchedule = null;
        }

        $this->dispatch('alert', type: 'success', message: 'Đã xóa lịch', sub: "Lịch #{$id} đã được xóa.");
        $this->dispatch('close-details-modal');
    }

    public function deleteFile(int $fileId)
    {
        if (!$this->selectedSchedule) {
            return;
        }

        $file = LabEventFile::where('lab_event_id', $this->selectedSchedule->id)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            $this->dispatch('alert', type: 'warning', message: 'File đã bị xóa trước đó.');
            return;
        }

        try {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();

            $this->selectedSchedule = $this->selectedSchedule->fresh('files');

            $this->dispatch('alert', type: 'success', message: 'Đã xóa file đính kèm.');
        } catch (\Throwable $e) {
            Log::error('Delete approval file error: ' . $e->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Xóa file thất bại, vui lòng thử lại.');
        }
    }

    public function downloadFile($fileId)
    {
        $file = LabEventFile::findOrFail($fileId);

        if (!Storage::exists($file->file_path)) {
            session()->flash('error', 'File không tồn tại trên hệ thống.');
            return;
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    public function render()
    {
        $now = Carbon::now();
        LabEvent::where('status', 'approved')
            ->where('end', '<', $now)
            ->update(['status' => 'completed']);

        $labs = Lab::select('code', 'name')->orderBy('name')->get();

        $schedules = LabEvent::with(['user', 'lab'])
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUserId !== '', fn($q) => $q->where('user_id', $this->filterUserId))
            ->when($this->filterDate !== '', fn($q) => $q->whereDate('start', $this->filterDate))
            ->when($this->filterLabCode !== '', fn($q) => $q->where('lab_code', $this->filterLabCode))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $groups = Group::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('livewire.approval', [
            'schedules' => $schedules,
            'pendingCount' => LabEvent::where('status', 'pending')->count(),
            'users' => User::select('id', 'full_name', 'email')->orderBy('full_name')->get(),
            'labs' => $labs,
            'groups' => $groups,
        ])->layout('components.layouts.admin-layout');
    }

    private function notifyUserEventResult(LabEvent $event, string $status): void
    {
        $userId = $event->user_id;
        if (!$userId)
            return;

        $admin = auth()->user();
        $statusText = $status === 'approved' ? 'đã được phê duyệt' : 'đã bị từ chối';

        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => "Kết quả duyệt lịch: " . $event->title,
            'message' => "Yêu cầu mượn phòng " . $event->lab_code . " của bạn " . $statusText . ".",
            'data' => [
                'event_id' => $event->id,
                'type' => 'event_result',
                'status' => $status,
                'admin_name' => $admin->full_name ?? $admin->name ?? 'Quản trị viên',
                'url' => route('home'),
            ],
        ]);
    }
}