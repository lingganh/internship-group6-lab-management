<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LabEvent;
use App\Models\Lab;
use App\Models\LabEventFile;
use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LabCalendar extends Component
{
    use WithFileUploads;

    // Modal states
    public $showEventModal = false;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $showConflictModal = false;

    // Form fields
    public $eventId = null;
    public $title = '';
    public $category = 'work';
    public $labCode = 'LAB-304';
    public $registeredFor = '';
    public $description = '';
    public $startDate = '';
    public $startTime = '09:00';
    public $endTime = '10:00';
    public $repeatType = '';
    public $repeatUntil = '';
    public $repeatDays = [];
    public $files = [];

    // Detail modal data
    public $detailEvent = null;

    // Filters
    public $hiddenStatuses = [];
    public $hiddenCategories = [];
    public $selectedRoomFilter = '';

    // Conflict data
    public $conflicts = [];
    public $pendingOccurrences = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'category' => 'required|string|in:work,seminar,other',
        'labCode' => 'required|string',
        'startDate' => 'required|date',
        'startTime' => 'required',
        'endTime' => 'required',
        'description' => 'nullable|string|max:1000',
        'registeredFor' => 'nullable|string|max:255',
        'files.*' => 'nullable|file|max:10240', // 10MB max
    ];

    public function mount()
    {
        $this->autoUpdateStatuses();
        $this->startDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $this->autoUpdateStatuses();

        $user = Auth::user();
        $groups = Group::select('id', 'name')
            ->when(!(!$user || (int) $user->role_id === 1), function ($q) use ($user) {
                $q->whereIn('id', function ($sub) use ($user) {
                    $sub->select('registered_for')
                        ->from('lab_events')
                        ->where('user_id', $user->id)
                        ->whereNotNull('registered_for');
                });
            })
            ->orderBy('name')
            ->get();

        $rooms = Lab::select('code', 'name')
            ->orderBy('name')
            ->get();

        $events = $this->getFilteredEvents();

        return view('livewire.lab-calendar', [
            'rooms' => $rooms,
            'groups' => $groups,
            'events' => $events,
        ])->layout('components.layouts.client-layout');
    }

    private function autoUpdateStatuses(): void
    {
        try {
            $now = Carbon::now();

            LabEvent::where('status', 'approved')
                ->where('end', '<', $now)
                ->update(['status' => 'completed']);

            LabEvent::query()
                ->where('status', 'pending')
                ->whereNotNull('start')
                ->where('start', '<=', $now)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => $now,
                ]);
        } catch (\Throwable $e) {
            Log::error('Auto update status error: ' . $e->getMessage());
        }
    }

    private function getFilteredEvents()
    {
        return LabEvent::with('lab:code,name')
            ->where('status', '!=', 'cancelled')
            ->when(!empty($this->hiddenStatuses), function ($q) {
                $q->whereNotIn('status', $this->hiddenStatuses);
            })
            ->when(!empty($this->hiddenCategories), function ($q) {
                $q->whereNotIn('category', $this->hiddenCategories);
            })
            ->when($this->selectedRoomFilter, function ($q) {
                $q->where('lab_code', $this->selectedRoomFilter);
            })
            ->orderBy('start')
            ->get()
            ->map(function ($event) {
                $statusColors = [
                    'pending' => '#f59e0b',
                    'approved' => '#10b981',
                    'completed' => '#6366f1',
                    'cancelled' => '#ef4444'
                ];

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'category' => $event->category,
                    'status' => $event->status,
                    'backgroundColor' => $statusColors[$event->status] ?? '#3788d8',
                    'extendedProps' => [
                        'description' => $event->description,
                        'roomCode' => $event->lab_code,
                        'roomName' => $event->lab?->name,
                        'user_id' => $event->user_id,
                        'registered_for' => $event->registered_for,
                        'registeredForName' => $event->registered_for ? 
                            Group::find($event->registered_for)?->name : null,
                    ]
                ];
            });
    }

    public function toggleStatusFilter($status)
    {
        if (in_array($status, $this->hiddenStatuses)) {
            $this->hiddenStatuses = array_diff($this->hiddenStatuses, [$status]);
        } else {
            $this->hiddenStatuses[] = $status;
        }
    }

    public function toggleCategoryFilter($category)
    {
        if (in_array($category, $this->hiddenCategories)) {
            $this->hiddenCategories = array_diff($this->hiddenCategories, [$category]);
        } else {
            $this->hiddenCategories[] = $category;
        }
    }

    public function openCreateModal($start = null, $end = null)
    {
        $this->resetForm();
        
        if ($start) {
            $startDate = Carbon::parse($start);
            if ($startDate->isPast()) {
                session()->flash('error', 'Không thể tạo sự kiện trong quá khứ');
                return;
            }
            
            $this->startDate = $startDate->format('Y-m-d');
            $this->startTime = $startDate->format('H:i');
            
            if ($end) {
                $this->endTime = Carbon::parse($end)->format('H:i');
            } else {
                $this->endTime = $startDate->addHour()->format('H:i');
            }
        }
        
        $this->showEventModal = true;
    }

    public function openEditModal($eventId)
    {
        $event = LabEvent::find($eventId);
        
        if (!$event) {
            session()->flash('error', 'Không tìm thấy sự kiện');
            return;
        }

        if (!$this->checkPermission($event)) {
            session()->flash('error', 'Bạn không có quyền chỉnh sửa sự kiện này');
            return;
        }

        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->category = $event->category;
        $this->labCode = $event->lab_code;
        $this->registeredFor = $event->registered_for ?? '';
        $this->description = $event->description ?? '';
        $this->startDate = Carbon::parse($event->start)->format('Y-m-d');
        $this->startTime = Carbon::parse($event->start)->format('H:i');
        $this->endTime = Carbon::parse($event->end)->format('H:i');
        
        $this->showEventModal = true;
    }

    public function showEventDetails($eventId)
    {
        $event = LabEvent::with('lab:code,name')->find($eventId);
        
        if (!$event) {
            return;
        }

        $this->detailEvent = [
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start,
            'end' => $event->end,
            'category' => $event->category,
            'status' => $event->status,
            'description' => $event->description,
            'roomName' => $event->lab?->name,
            'registeredForName' => $event->registered_for ? 
                Group::find($event->registered_for)?->name : null,
            'user_id' => $event->user_id,
            'canEdit' => $this->checkPermission($event),
        ];

        $this->showDetailModal = true;
    }

    private function checkPermission($event): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $isAdmin = $user->code === 'admin' || (int) $user->role_id === 1;

        if ($isAdmin) {
            return true;
        }

        if ($event->user_id != $user->id) {
            return false;
        }

        if ($event->status === 'approved') {
            $eventStart = Carbon::parse($event->start);
            if ($eventStart->isPast()) {
                return false;
            }
        }

        return true;
    }

    public function saveEvent($force = false)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Bạn cần đăng nhập');
            return;
        }

        $this->validate();

        $occurrences = $this->buildOccurrences();

        if (!$occurrences['ok']) {
            session()->flash('error', $occurrences['message'] ?? 'Không tạo được lịch nào');
            return;
        }

        $user = Auth::user();
        $seriesId = Str::uuid()->toString();
        $createdEvents = [];
        $conflicts = [];

        foreach ($occurrences['occurrences'] as $occ) {
            // Check conflict
            $conflictedEvent = LabEvent::where('lab_code', $this->labCode)
                ->where('status', 'approved')
                ->where('id', '!=', $this->eventId ?? 0)
                ->where('start', '<', $occ['end'])
                ->where('end', '>', $occ['start'])
                ->first();

            if ($conflictedEvent) {
                $conflicts[] = [
                    'requested_start' => Carbon::parse($occ['start'])->format('d/m/Y H:i'),
                    'requested_end' => Carbon::parse($occ['end'])->format('d/m/Y H:i'),
                    'conflict_with' => [
                        'title' => $conflictedEvent->title,
                        'start' => Carbon::parse($conflictedEvent->start)->format('d/m/Y H:i'),
                        'end' => Carbon::parse($conflictedEvent->end)->format('d/m/Y H:i')
                    ]
                ];
                
                if (!$force) {
                    continue;
                }
                continue;
            }

            // Create or update event
            if ($this->eventId) {
                // Update existing event
                $event = LabEvent::find($this->eventId);
                if ($event) {
                    $event->update([
                        'title' => $this->title,
                        'category' => $this->category,
                        'lab_code' => $this->labCode,
                        'start' => $occ['start'],
                        'end' => $occ['end'],
                        'description' => $this->description,
                        'registered_for' => $this->registeredFor,
                        'updated_by' => $user->id,
                    ]);
                    $createdEvents[] = $event;
                }
                break; // Only update one event
            } else {
                // Create new event
                $event = LabEvent::create([
                    'series_id' => count($occurrences['occurrences']) > 1 ? $seriesId : null,
                    'title' => $this->title,
                    'category' => $this->category,
                    'lab_code' => $this->labCode,
                    'start' => $occ['start'],
                    'end' => $occ['end'],
                    'description' => $this->description,
                    'registered_for' => $this->registeredFor,
                    'status' => 'pending',
                    'user_id' => $user->id,
                ]);
                $createdEvents[] = $event;
            }
        }

        // Handle conflicts
        if (!$force && !empty($conflicts)) {
            $this->conflicts = $conflicts;
            $this->pendingOccurrences = $occurrences['occurrences'];
            $this->showConflictModal = true;
            return;
        }

        if (empty($createdEvents)) {
            session()->flash('error', 'Không có lịch nào hợp lệ để tạo.');
            return;
        }

        // Handle file uploads
        if (!empty($this->files)) {
            foreach ($createdEvents as $event) {
                $this->handleFileUploads($event->id);
            }
        }

        // Notify admins
        if ($user->role_id != 1) {
            $this->notifyAdminsPendingEvent($createdEvents[0], 
                $this->eventId ? 'updated' : 'created', 
                count($createdEvents)
            );
        }

        $message = $this->eventId 
            ? 'Cập nhật sự kiện thành công'
            : 'Đã gửi yêu cầu đăng ký thành công (' . count($createdEvents) . ' lịch).';
            
        session()->flash('success', $message);
        $this->closeModal();
    }

    public function confirmContinue()
    {
        $this->saveEvent(true);
    }

    private function buildOccurrences(): array
    {
        if (!$this->startDate || !$this->startTime || !$this->endTime) {
            return ['ok' => false, 'message' => 'Thiếu ngày/giờ.'];
        }

        $start = Carbon::parse("{$this->startDate} {$this->startTime}");
        $end = Carbon::parse("{$this->startDate} {$this->endTime}");

        if ($end <= $start) {
            return ['ok' => false, 'message' => 'Giờ kết thúc phải sau giờ bắt đầu.'];
        }

        $duration = $end->diffInSeconds($start);

        // No repeat
        if (!$this->repeatType || !$this->repeatUntil) {
            return [
                'ok' => true,
                'occurrences' => [
                    ['start' => $start->toDateTimeString(), 'end' => $end->toDateTimeString()]
                ]
            ];
        }

        $repeatUntil = Carbon::parse($this->repeatUntil)->endOfDay();
        
        if ($repeatUntil < $start) {
            return ['ok' => false, 'message' => 'Ngày lặp đến phải sau hoặc bằng ngày bắt đầu.'];
        }

        $occurrences = [];
        $cursor = $start->copy();

        if ($this->repeatType === 'daily') {
            while ($cursor <= $repeatUntil && count($occurrences) < 500) {
                $occurrences[] = [
                    'start' => $cursor->toDateTimeString(),
                    'end' => $cursor->copy()->addSeconds($duration)->toDateTimeString()
                ];
                $cursor->addDay();
            }
        } elseif ($this->repeatType === 'weekly') {
            if (empty($this->repeatDays)) {
                return ['ok' => false, 'message' => 'Hãy chọn ít nhất 1 ngày trong tuần để lặp.'];
            }

            $cursor->startOfDay();
            while ($cursor <= $repeatUntil && count($occurrences) < 500) {
                if (in_array($cursor->dayOfWeek, $this->repeatDays)) {
                    $eventStart = $cursor->copy()
                        ->setTime($start->hour, $start->minute, $start->second);
                    $occurrences[] = [
                        'start' => $eventStart->toDateTimeString(),
                        'end' => $eventStart->copy()->addSeconds($duration)->toDateTimeString()
                    ];
                }
                $cursor->addDay();
            }
        } elseif ($this->repeatType === 'monthly') {
            while ($cursor <= $repeatUntil && count($occurrences) < 500) {
                $occurrences[] = [
                    'start' => $cursor->toDateTimeString(),
                    'end' => $cursor->copy()->addSeconds($duration)->toDateTimeString()
                ];
                $cursor->addMonth();
            }
        }

        if (empty($occurrences)) {
            return ['ok' => false, 'message' => 'Không tạo được lịch nào.'];
        }

        return ['ok' => true, 'occurrences' => $occurrences];
    }

    public function deleteEvent()
    {
        if (!$this->detailEvent) {
            return;
        }

        $event = LabEvent::find($this->detailEvent['id']);
        
        if (!$event || !$this->checkPermission($event)) {
            session()->flash('error', 'Bạn không có quyền xóa sự kiện này');
            return;
        }

        $this->showDetailModal = false;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $event = LabEvent::find($this->detailEvent['id']);
        
        if (!$event) {
            session()->flash('error', 'Không tìm thấy sự kiện');
            $this->closeModal();
            return;
        }

        try {
            // Delete files
            $files = LabEventFile::where('lab_event_id', $event->id)->get();
            foreach ($files as $file) {
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
                $file->delete();
            }

            $wasApproved = $event->status === 'approved';
            
            if ($wasApproved) {
                $event->update(['status' => 'cancelled', 'updated_at' => now()]);
                $message = 'Lịch đã duyệt đã được chuyển sang trạng thái hủy.';
                $this->notifyAdminsDeletedEvent($event);
            } else {
                $event->delete();
                $message = 'Đã xóa sự kiện thành công.';
            }

            session()->flash('success', $message);
        } catch (\Throwable $e) {
            Log::error('Delete event error: ' . $e->getMessage());
            session()->flash('error', 'Có lỗi xảy ra khi xóa sự kiện.');
        }

        $this->closeModal();
    }

    private function handleFileUploads($eventId)
    {
        foreach ($this->files as $file) {
            try {
                $path = $file->store('lab_files', 'public');

                LabEventFile::create([
                    'lab_event_id' => $eventId,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage());
            }
        }
    }

    private function notifyAdminsPendingEvent(LabEvent $event, string $action = 'created', int $totalOccurrences = 1): void
    {
        $user = Auth::user();
        $admins = User::where('role_id', 1)->get();

        if ($admins->isEmpty()) {
            return;
        }

        $title = $action === 'created' ? 'Lịch đặt phòng mới chờ duyệt' : 'Lịch đặt phòng đã được cập nhật';
        $senderName = $user->full_name ?? $user->name ?? 'Người dùng';
        $eventInfo = $totalOccurrences > 1
            ? "{$event->title} ({$totalOccurrences} lịch lặp)"
            : $event->title;

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => "{$senderName} đã đăng ký: {$eventInfo} tại phòng {$event->lab_code}",
                'data' => [
                    'event_id' => $event->id,
                    'type' => 'pending_event',
                    'sender_name' => $senderName,
                    'url' => route('admin.approval'),
                ],
            ]);
        }
    }

    private function notifyAdminsDeletedEvent(LabEvent $event): void
    {
        $user = Auth::user();
        $admins = User::where('role_id', 1)->get();

        if ($admins->isEmpty()) {
            return;
        }

        $senderName = $user->full_name ?? $user->name ?? 'Người dùng';
        $title = $event->status === 'cancelled' 
            ? 'Lịch đã duyệt bị hủy' 
            : 'Lịch đặt phòng đã bị xóa';
        $message = $event->status === 'cancelled'
            ? "{$senderName} đã hủy lịch đã duyệt: {$event->title} tại phòng {$event->lab_code}"
            : "{$senderName} đã xóa lịch: {$event->title} tại phòng {$event->lab_code}";

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'event_id' => $event->id,
                    'type' => $event->status === 'cancelled' ? 'event_cancelled' : 'event_deleted',
                    'sender_name' => $senderName,
                    'url' => route('admin.approval'),
                ],
            ]);
        }
    }

    private function resetForm()
    {
        $this->eventId = null;
        $this->title = '';
        $this->category = 'work';
        $this->labCode = 'LAB-304';
        $this->registeredFor = '';
        $this->description = '';
        $this->startDate = now()->format('Y-m-d');
        $this->startTime = '09:00';
        $this->endTime = '10:00';
        $this->repeatType = '';
        $this->repeatUntil = '';
        $this->repeatDays = [];
        $this->files = [];
    }

    public function closeModal()
    {
        $this->showEventModal = false;
        $this->showDetailModal = false;
        $this->showDeleteModal = false;
        $this->showConflictModal = false;
        $this->detailEvent = null;
        $this->conflicts = [];
        $this->resetForm();
        $this->resetValidation();
    }
}