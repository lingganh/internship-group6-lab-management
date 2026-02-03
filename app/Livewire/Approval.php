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
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Loading states
    public $isApproving = false;
    public $isRejecting = false;
    public $isDeleting = false;
    public $isSaving = false;

    // Filters
    public $filterStatus = '';
    public $filterLabCode = '';
    public $filterUserId = '';
    public $filterDate = '';

    // Selection
    public array $selectedIds = [];

    // Edit form
    public $selectedSchedule = null;
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

    // Confirmation
    public $confirmType = '';
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $confirmId = null;
    public $rejectionNote = '';

    // Approval
    public $passwordModalId = null;
    public $roomCode = '';
    public int $seriesApproveCount = 0;

    // Conflict
    public $conflictId = null;
    public $conflictSchedule = null;
    // Conflict (single)
    public array $conflictDetails = [];

    // Conflict (batch/series)
    public array $batchCandidateIds = [];
    public array $batchOkIds = [];
    public array $batchConflictIds = [];
    public array $batchConflictDetails = [];
    public int $batchOkCount = 0;
    public int $batchConflictCount = 0;

    public bool $batchForceApproveConflicts = false;

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

    public function updatingFilterLabCode()
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
    private function getConflictEvents(LabEvent $event)
    {
        return LabEvent::query()
            ->with(['user:id,full_name', 'lab:id,code,name'])
            ->where('lab_code', $event->lab_code)
            ->whereIn('status', ['approved', 'completed'])
            ->where('id', '!=', $event->id)
            ->where('start', '<', $event->end)
            ->where('end', '>', $event->start)
            ->orderBy('start')
            ->get(['id', 'title', 'lab_code', 'start', 'end', 'status', 'user_id']);
    }

 public array $conflictPreviewList = [];

public function previewRejectConflicts()
{
    // Tìm các lịch pending bị trùng giờ (Sử dụng whereExists tối ưu hơn)
    $conflicts = LabEvent::where('status', 'pending')
        ->whereExists(function ($query) {
            $query->select(\DB::raw(1))
                ->from('lab_events as approved')
                ->whereIn('approved.status', ['approved', 'completed'])
                ->whereColumn('approved.lab_code', 'lab_events.lab_code')
                ->whereColumn('approved.id', '<>', 'lab_events.id')
                ->whereRaw('approved.start < lab_events.end')
                ->whereRaw('approved.end > lab_events.start');
        })
        ->with(['user:id,full_name', 'lab:id,name'])
        ->get();

    if ($conflicts->isEmpty()) {
        $this->dispatch('alert', type: 'info', message: 'Không tìm thấy lịch nào bị trùng.');
        return;
    }

    $this->conflictPreviewList = $conflicts->map(fn($ev) => [
        'id' => $ev->id,
        'title' => $ev->title,
        'lab' => $ev->lab_code,
        'user' => $ev->user->full_name ?? 'N/A',
        'time' => $ev->start->format('d/m H:i') . ' - ' . $ev->end->format('H:i'),
    ])->toArray();

    $this->dispatch('open-preview-conflict-modal');
}

public function rejectAllConflicts()
{
    // 1. Lấy danh sách ID từ bản xem trước để xử lý chính xác những gì Admin thấy
    $ids = collect($this->conflictPreviewList)->pluck('id');
    
    if ($ids->isEmpty()) {
        $this->dispatch('close-preview-conflict-modal');
        return;
    }

    $reason = 'Lịch bị trùng với lịch đã được phê duyệt trước đó. Vui lòng chọn khung giờ khác.';
    
    // 2. Lấy dữ liệu và thực hiện cập nhật
    $eventsToReject = LabEvent::whereIn('id', $ids)->with('user')->get();
    
    foreach ($eventsToReject as $event) {
        $event->update(['status' => 'cancelled', 'reason' => $reason]);

        // Thông báo qua hệ thống và Email
        $this->notifyUserEventResult($event, 'rejected');
        
        if ($event->user && $event->user->email) {
            Mail::to($event->user->email)->queue(
                new \App\Mail\RejectionNotification($event, $reason)
            );
        }
    }

    // 3. Dọn dẹp và đóng Modal thông qua Alpine.js
    $this->conflictPreviewList = [];
    $this->dispatch('close-preview-conflict-modal');  
    $this->dispatch('alert', type: 'success', message: 'Đã xử lý từ chối các lịch trùng!');
}
    private function makeConflictPayload(LabEvent $event, $conflicts): array
    {
        $fmt = fn($dt) => Carbon::parse($dt)->format('d/m/Y H:i');

        return [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'lab_code' => $event->lab_code,
                'lab_name' => $event->lab?->name,
                'start' => $fmt($event->start),
                'end' => $fmt($event->end),
                'user' => $event->user?->full_name,
                'series_id' => $event->series_id,
            ],
            'conflicts' => $conflicts->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'lab_code' => $c->lab_code,
                'lab_name' => $c->lab?->name,
                'start' => $fmt($c->start),
                'end' => $fmt($c->end),
                'status' => $c->status,
                'user' => $c->user?->full_name,
            ])->toArray(),
        ];
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

    public function toggleSelectAll()
    {
        if (empty($this->selectedIds)) {
            $this->selectedIds = LabEvent::where('status', 'pending')
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedIds = [];
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

    public function removeNewFile($index)
    {
        if (isset($this->newFiles[$index])) {
            unset($this->newFiles[$index]);
            $this->newFiles = array_values($this->newFiles);
        }
    }

    public function approveNow($id)
    {
        $schedule = LabEvent::findOrFail($id);

        if ($schedule->status !== 'pending') {
            $this->dispatch(
                'alert',
                type: 'warning',
                message: 'Không thể phê duyệt',
                sub: 'Lịch không còn ở trạng thái chờ.'
            );
            return;
        }

        $this->passwordModalId = $schedule->id;
        $this->roomCode = '';

        // Hiển thị thông tin series
        if ($schedule->series_id) {
            $this->seriesApproveCount = LabEvent::where('series_id', $schedule->series_id)->count();
        } else {
            $this->seriesApproveCount = 0;
        }

        $this->dispatch('open-password-modal');
    }

    public function approveSelected()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('alert', type: 'warning', message: 'Chưa chọn lịch nào.');
            return;
        }

        $events = LabEvent::with(['user:id,full_name', 'lab:id,code,name'])
            ->whereIn('id', $this->selectedIds)
            ->where('status', 'pending')
            ->orderBy('start')
            ->get();

        if ($events->isEmpty()) {
            $this->dispatch('alert', type: 'warning', message: 'Không có lịch chờ duyệt.');
            return;
        }

        // reset state
        $this->batchCandidateIds = $events->pluck('id')->toArray();
        $this->batchOkIds = [];
        $this->batchConflictIds = [];
        $this->batchConflictDetails = [];
        $this->batchForceApproveConflicts = false;

        // Nhóm các lịch theo lab_code để kiểm tra nhanh hơn
        $eventsByLab = $events->groupBy('lab_code');

        foreach ($events as $event) {
            $hasConflict = false;
            
            // 1. Kiểm tra trùng với lịch đã approved/completed
            $externalConflicts = $this->getConflictEvents($event);
            
            if ($externalConflicts->isNotEmpty()) {
                $hasConflict = true;
                $this->batchConflictIds[] = $event->id;
                $this->batchConflictDetails[] = $this->makeConflictPayload($event, $externalConflicts);
                continue;
            }

            // 2. Kiểm tra trùng với các lịch khác trong batch (cùng lab)
            $sameLab = $eventsByLab[$event->lab_code] ?? collect();
            $internalConflicts = $sameLab->filter(function($other) use ($event) {
                return $other->id !== $event->id 
                    && $other->start < $event->end 
                    && $other->end > $event->start;
            });

            if ($internalConflicts->isNotEmpty()) {
                $hasConflict = true;
                $this->batchConflictIds[] = $event->id;
                $this->batchConflictDetails[] = $this->makeConflictPayload($event, $internalConflicts);
                continue;
            }

            // Không có conflict
            if (!$hasConflict) {
                $this->batchOkIds[] = $event->id;
            }
        }

        $this->batchOkCount = count($this->batchOkIds);
        $this->batchConflictCount = count($this->batchConflictIds);

        // có trùng => mở modal
        if ($this->batchConflictCount > 0) {
            $this->dispatch('open-batch-conflict-modal');
            return;
        }

        // không trùng => mở password modal
        $this->passwordModalId = 'batch';
        $this->seriesApproveCount = count($this->batchCandidateIds);
        $this->dispatch('open-password-modal');
    }
    
    public function continueBatchAfterConflict()
    {
        $idsToApprove = $this->batchForceApproveConflicts
            ? $this->batchCandidateIds
            : $this->batchOkIds;

        if (empty($idsToApprove)) {
            $this->dispatch('alert', type: 'warning', message: 'Không có lịch nào có thể duyệt', sub: 'Tất cả lịch đã chọn đều bị trùng.');
            $this->dispatch('close-batch-conflict-modal');
            return;
        }

        // gán lại selectedIds => approveSchedule() nhánh batch dùng bình thường
        $this->selectedIds = $idsToApprove;

        $this->passwordModalId = 'batch';
        $this->seriesApproveCount = count($idsToApprove);

        $this->dispatch('close-batch-conflict-modal');
        $this->dispatch('open-password-modal');
    }

    public function cancelBatchConflict()
    {
        $this->dispatch('close-batch-conflict-modal');
        $this->batchCandidateIds = [];
        $this->batchOkIds = [];
        $this->batchConflictIds = [];
        $this->batchConflictDetails = [];
        $this->batchOkCount = 0;
        $this->batchConflictCount = 0;
        $this->batchForceApproveConflicts = false;
    }


    public function rejectSelected()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch(
                'alert',
                type: 'warning',
                message: 'Chưa chọn lịch nào.'
            );
            return;
        }

        // Chỉ lấy lịch pending
        $count = LabEvent::whereIn('id', $this->selectedIds)
            ->where('status', 'pending')
            ->count();

        if ($count === 0) {
            $this->dispatch(
                'alert',
                type: 'warning',
                message: 'Không có lịch chờ duyệt.'
            );
            return;
        }

        // Mở modal confirm với type batch-reject
        $this->confirmType = 'batch-reject';
        $this->confirmTitle = 'Từ chối nhiều lịch';
        $this->confirmMessage = "Bạn chắc chắn muốn từ chối {$count} lịch đã chọn?";
        $this->confirmId = null; // Không cần ID cụ thể
        $this->rejectionNote = '';

        $this->dispatch('open-confirm-modal');
    }

    public function approveSchedule()
    {
        if ($this->isApproving) {
            return;
        }

        $this->isApproving = true;
        $roomCode = $this->roomCode; 
        try {
            if ($this->passwordModalId === 'batch') {
                // Duyệt hàng loạt - sử dụng chunk
                $eventIds = $this->selectedIds;
                // Capture roomCode trước khi vào closure
                $count = 0;

                // Thu thập thông tin để gửi thông báo gộp
                $notificationsByUser = [];
                $emailsByUser = [];

                LabEvent::whereIn('id', $eventIds)
                    ->where('status', 'pending')
                    ->chunk(50, function ($events) use (&$count, $roomCode, &$notificationsByUser, &$emailsByUser) {
                        foreach ($events as $event) {
                            $event->update(['status' => 'approved']);
                            $count++;

                            $userId = $event->user_id;
                            if (!$userId) continue;

                            // Nhóm theo user_id và series_id
                            $key = $userId;
                            if (!isset($notificationsByUser[$key])) {
                                $notificationsByUser[$key] = [];
                            }

                            // Nhóm theo series_id (nếu có) hoặc event đơn lẻ
                            $seriesKey = $event->series_id ?: 'single_' . $event->id;
                            
                            if (!isset($notificationsByUser[$key][$seriesKey])) {
                                $notificationsByUser[$key][$seriesKey] = [
                                    'events' => [],
                                    'user' => $event->user,
                                    'series_id' => $event->series_id,
                                ];
                            }

                            $notificationsByUser[$key][$seriesKey]['events'][] = $event;
                        }
                    });

                // Gửi thông báo gộp sau khi duyệt xong
                dispatch(function () use ($notificationsByUser, $roomCode) {
                    foreach ($notificationsByUser as $userId => $seriesGroups) {
                        // Đếm tổng số lịch của user
                        $totalEvents = 0;
                        foreach ($seriesGroups as $data) {
                            $totalEvents += count($data['events']);
                        }

                        // Nếu user chỉ có 1 lịch duy nhất
                        if ($totalEvents === 1) {
                            $firstGroup = reset($seriesGroups);
                            $event = $firstGroup['events'][0];
                            $this->notifyUserEventResult($event, 'approved');
                
                            if ($event->user && $event->user->email) {
                                Mail::to($event->user->email)->queue(
                                    new \App\Mail\ApprovalNotification($event, $roomCode)
                                );
                            }
                            continue;
                        }

                        // User có nhiều lịch → gửi 1 thông báo gộp
                        $allEvents = [];
                        foreach ($seriesGroups as $data) {
                            $allEvents = array_merge($allEvents, $data['events']);
                        }

                        $user = reset($seriesGroups)['user'];
                        
                        // Tạo 1 thông báo duy nhất
                        \App\Models\Notification::create([
                            'user_id' => $userId,
                            'title' => "Đã phê duyệt {$totalEvents} lịch sử dụng phòng lab",
                            'message' => "Tất cả {$totalEvents} lịch đăng ký của bạn đã được phê duyệt.",
                            'data' => [
                                'type' => 'batch_approved',
                                'event_ids' => collect($allEvents)->pluck('id')->toArray(),
                                'event_count' => $totalEvents,
                                'url' => route('home'),
                            ],
                        ]);

                        // Gửi email theo từng nhóm
                        foreach ($seriesGroups as $seriesKey => $data) {
                            $events = $data['events'];
                            $seriesId = $data['series_id'];
                            $eventCount = count($events);

                            if ($user && $user->email) {
                                if ($seriesId && $eventCount > 1) {
                                    // Email cho lịch lặp
                                    Mail::to($user->email)->queue(
                                        new \App\Mail\SeriesApprovalNotification(collect($events), $roomCode)
                                    );
                                } else {
                                    // Email cho từng lịch đơn
                                    foreach ($events as $event) {
                                        Mail::to($user->email)->queue(
                                            new \App\Mail\ApprovalNotification($event, $roomCode)
                                        );
                                    }
                                }
                            }
                        }
                    }
                })->afterResponse();

                $this->selectedIds = [];
                $this->dispatch(
                    'alert',
                    type: 'success',
                    message: "Đã phê duyệt {$count} lịch",
                    sub: 'Email sẽ được gửi trong giây lát.'
                );
            } else {
                // Duyệt đơn lẻ
                $event = LabEvent::with(['lab', 'user'])->findOrFail($this->passwordModalId);

                // CHECK TRÙNG
                $conflicts = $this->getConflictEvents($event);
                if ($conflicts->isNotEmpty()) {
                    $this->conflictId = $event->id;
                    $this->conflictDetails = $this->makeConflictPayload($event, $conflicts);

                    $this->dispatch('close-password-modal');
                    $this->dispatch('open-conflict-modal');
                    return;
                }

                $event->update(['status' => 'approved']);

                // Queue notification và email
                dispatch(function () use ($event, $roomCode) {
                    $this->notifyUserEventResult($event, 'approved');

                    if ($event->user && $event->user->email) {
                        Mail::to($event->user->email)->queue(
                            new \App\Mail\ApprovalNotification($event, $roomCode)
                        );
                    }
                })->afterResponse();

                $this->dispatch(
                    'alert',
                    type: 'success',
                    message: 'Đã phê duyệt lịch',
                    sub: $event->series_id ? 'Đây là 1 buổi trong lịch lặp.' : null
                );
            }

            $this->dispatch('close-password-modal');
            $this->dispatch('close-details-modal');
            $this->passwordModalId = null;
            $this->roomCode = '';
        } finally {
            $this->isApproving = false;
        }
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

        $this->dispatch('open-confirm-modal');
    }

    public function performConfirm()
    {
        if ($this->confirmType === 'reject' && !$this->confirmId) {
            $this->dispatch('alert', type: 'error', message: 'Không hợp lệ', sub: 'Thiếu dữ liệu xác nhận.');
            return;
        }

        // Kiểm tra lý do từ chối
        if (($this->confirmType === 'reject' || $this->confirmType === 'batch-reject') && trim($this->rejectionNote) === '') {
            $this->dispatch('alert', type: 'warning', message: 'Thiếu lý do', sub: 'Vui lòng nhập lý do từ chối.');
            return;
        }

        if ($this->confirmType === 'reject') {
            $this->rejectSchedule($this->confirmId);
        } elseif ($this->confirmType === 'batch-reject') {
            $this->rejectScheduleBatch();
        } elseif ($this->confirmType === 'delete') {
            $this->deleteSchedule($this->confirmId);
        }

        $this->dispatch('close-confirm-modal');

        $this->confirmType = '';
        $this->confirmId = null;
        $this->rejectionNote = '';
    }

    public function rejectSchedule($id)
    {
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

    public function rejectScheduleBatch()
    {
        if ($this->isRejecting) {
            return;
        }

        $this->isRejecting = true;

        try {
            // Lấy toàn bộ lịch pending đã chọn
            $events = LabEvent::with('user')
                ->whereIn('id', $this->selectedIds)
                ->where('status', 'pending')
                ->get();

            if ($events->isEmpty()) {
                $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: 'Không có lịch chờ duyệt.'
                );
                return;
            }

            $reason = trim($this->rejectionNote) ?: null;

            // Update status hàng loạt
            LabEvent::whereIn('id', $events->pluck('id'))
                ->update([
                    'status' => 'cancelled',
                    'reason' => $reason,
                ]);

            // Gửi email và notification cho từng user
            $userEmails = $events->pluck('user.email')->filter()->unique();

            foreach ($userEmails as $email) {
                $userEvents = $events->filter(fn($e) => $e->user?->email === $email);

                if ($userEvents->isNotEmpty()) {
                    // Gửi 1 email cho nhiều lịch của cùng user
                    Mail::to($email)->queue(
                        new \App\Mail\BatchRejectionNotification(
                            $userEvents,
                            $this->rejectionNote
                        )
                    );

                    // Tạo notification
                    $userId = $userEvents->first()->user_id;
                    if ($userId) {
                        \App\Models\Notification::create([
                            'user_id' => $userId,
                            'title' => 'Lịch đã bị từ chối',
                            'message' => 'Bạn có ' . $userEvents->count() . ' lịch đã bị từ chối. Lý do: ' . ($reason ?: 'Không có lý do cụ thể'),
                            'data' => [
                                'type' => 'batch_rejected',
                                'event_ids' => $userEvents->pluck('id')->toArray(),
                                'reason' => $reason,
                                'url' => route('home'),
                            ],
                        ]);
                    }
                }
            }

            // Reset state
            $count = $events->count();
            $this->selectedIds = [];
            $this->selectedCount = 0;

            $this->dispatch(
                'alert',
                type: 'success',
                message: "Đã từ chối {$count} lịch",
                sub: 'Email thông báo đã được gửi.'
            );

        } finally {
            $this->isRejecting = false;
        }
    }

    public function deleteSchedule($id)
    {
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

    public function forceApprove()
    {
        if (!$this->conflictId)
            return;

        $event = LabEvent::findOrFail($this->conflictId);

        if ($event->status !== 'pending') {
            $this->dispatch('alert', type: 'warning', message: 'Không thể xử lý', sub: 'Lịch không còn ở trạng thái chờ.');
            $this->conflictId = null;
            $this->conflictDetails = [];
            $this->dispatch('close-conflict-modal');
            return;
        }

        $this->passwordModalId = $event->id;
        $this->roomCode = '';

        $this->dispatch('close-conflict-modal');
        $this->dispatch('open-password-modal');
    }


    private function notifyUserEventResult(LabEvent $event, string $status): void
    {
        $userId = $event->user_id;
        if (!$userId) {
            return;
        }

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

        public function render()
    {
        // Tự động chuyển status completed
        $now = Carbon::now();
        LabEvent::where('status', 'approved')
            ->where('end', '<', $now)
            ->update(['status' => 'completed']);

        // Query schedules với tối ưu hóa
        $schedules = LabEvent::select([
            'id',
            'title',
            'category',
            'lab_code',
            'user_id',
            'start',
            'end',
            'status',
            'series_id',
            'created_at'
        ])
            ->with([
                'user:id,full_name,email',
                'lab:id,code,name'
            ])
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUserId !== '', fn($q) => $q->where('user_id', $this->filterUserId))
            ->when($this->filterDate !== '', fn($q) => $q->whereDate('start', $this->filterDate))
            ->when($this->filterLabCode !== '', fn($q) => $q->where('lab_code', $this->filterLabCode))
            ->orderBy('start', 'asc')       // Ưu tiên 1: Lịch sắp diễn ra hiện lên trước
            ->orderBy('created_at', 'asc')  // Ưu tiên 2: Cùng giờ bắt đầu thì thằng nào đký trước hiện trước
            ->paginate(15);

        // Đếm pending với CÙNG filters - QUAN TRỌNG!
        $pendingQuery = LabEvent::where('status', 'pending');

        if ($this->filterUserId !== '') {
            $pendingQuery->where('user_id', $this->filterUserId);
        }
        if ($this->filterDate !== '') {
            $pendingQuery->whereDate('start', $this->filterDate);
        }
        if ($this->filterLabCode !== '') {
            $pendingQuery->where('lab_code', $this->filterLabCode);
        }

        $pendingCount = $pendingQuery->count();

        $labs = Lab::select('code', 'name')->orderBy('name')->get();
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        return view('livewire.approval', [
            'schedules' => $schedules,
            'pendingCount' => $pendingCount,
            'users' => User::select('id', 'full_name', 'email')->orderBy('full_name')->get(),
            'labs' => $labs,
            'groups' => $groups,
        ])->layout('components.layouts.admin-layout');
    }
}