<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LabEvent;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class UserSchedules extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterDate = '';
    public $searchTerm = '';
    public $selectedSchedule = null;
    public $comment = '';
    public ?int $selectedEventId = null;
    public string $feedbackModalKey = '';
    public bool $feedbackLocked = false;

    public ?LabEvent $selectedEvent = null;

    protected $paginationTheme = 'bootstrap';

    // feedback tạo Phản hồi thành công
    public ?string $feedbackSuccessMessage = null;

   
    
    public function render()
    {
        $query = LabEvent::query()
            ->with('user', 'lab')
            ->where('user_id', auth()->id());
            // Đã bỏ ->whereNot('status', 'cancelled') để hiển thị cả lịch đã hủy

        // Tìm kiếm theo tên sự kiện hoặc mã phòng
        if ($this->searchTerm !== '') {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('lab_code', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('lab', function($labQuery) {
                      $labQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

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

    public function openFeedback(int $eventId): void
    {
        $event = LabEvent::with('lab')->findOrFail($eventId);

        $this->selectedEventId = $event->id;
        $this->selectedEvent   = $event;

        $locked = filled($event->feedback);

        if (! $locked && ! $this->canFeedback($event)) {
            $this->dispatch('toaster', 'Chưa thể phản hồi');
            return;
        }

        $this->feedbackSuccessMessage = null;

        // mở modal
        $this->dispatch('open-modal', id: 'feedbackModal');

        // load dữ liệu cho component con
        $this->dispatch('initIssueFromEvent', eventId: $event->id);

        if ($locked) {
            $this->dispatch('toaster', 'Lịch này bạn đã gửi phản hồi rồi. Bạn chỉ xem lại thôi.');
        }
    }

    public $scheduleIdToCancel = null;

    // Method xác nhận trước khi hủy
    public function confirmCancel($id): void
    {
        $event = LabEvent::find($id);
        
        if (!$event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }
        
        if (!$this->canCancel($event)) {
            $this->dispatch('toaster', 'Không thể hủy (chỉ hủy trước giờ bắt đầu tối thiểu 1 giờ)');
            return;
        }
        
        $this->scheduleIdToCancel = $id;
        $this->dispatch('open-modal', id: 'modalConfirmCancel');
    }

    // Method thực hiện hủy lịch - chuyển status sang 'cancelled'
    public function cancelSchedule(): void
    {
        if (!$this->scheduleIdToCancel) {
            return;
        }
        
        $event = LabEvent::find($this->scheduleIdToCancel);
        
        if (!$event) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }
        
        // Chuyển status sang 'cancelled' thay vì xóa
        $event->status = 'cancelled';
        $event->updated_by = Auth::id();
        $event->updated_at = now();
        $event->save();
        
        // Gửi thông báo cho admin
        $this->sendCancellationNotificationToAdmin($event);
        
        $this->dispatch('close-modal', id: 'modalConfirmCancel');
        $this->dispatch('toaster', 'Đã hủy lịch trình thành công');
        
        $this->scheduleIdToCancel = null;
    }
    
    /**
     * Gửi thông báo hủy lịch cho admin
     */
    private function sendCancellationNotificationToAdmin(LabEvent $event): void
    {
        try {
            // Lấy danh sách admin (role = admin hoặc super_admin)
            $admins = \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super_admin']);
            })->get();
            
            $user = Auth::user();
            
            // Tạo thông báo cho từng admin
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Lịch đăng ký bị hủy: ' . $event->title,
                    'message' => sprintf(
                        '%s đã hủy lịch mượn phòng %s (Thời gian: %s)',
                        $user->full_name ?? $user->name,
                        $event->lab_code ?? 'N/A',
                        Carbon::parse($event->start)->format('H:i d/m/Y')
                    ),
                    'data' => [
                        'event_id' => $event->id,
                        'type' => 'event_cancelled',
                        'event_title' => $event->title,
                        'lab_code' => $event->lab_code,
                        'event_start' => $event->start,
                        'cancelled_by' => $user->id,
                        'cancelled_by_name' => $user->full_name ?? $user->name,
                        'cancelled_at' => now()->toISOString(),
                        'url' => route('admin.schedules.index'), // Hoặc route phù hợp
                    ],
                ]);
            }
            
        } catch (\Exception $e) {
            // Log lỗi nhưng không làm gián đoạn quá trình hủy lịch
            \Log::error('Failed to send cancellation notification to admin: ' . $e->getMessage());
        }
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

        if (! $this->selectedEventId) {
            $this->dispatch('toaster', 'Không tìm thấy lịch');
            return;
        }

        $this->dispatch('submitIssueRequest');

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

    public function submitIssueRequest(): void
    {
        $this->dispatch('submitIssueRequest');
    }

    #[\Livewire\Attributes\On('issueRequestCreated')]
    public function onIssueRequestCreated(int $requestId): void
    {
        // $this->feedbackSuccessMessage = 'Đã gửi ý kiến phản hồi. Vui lòng chờ admin xử lý.';
        $this->dispatch('toaster', 'Đã gửi ý kiến phản hồi. Vui lòng chờ admin xử lý.');

        $this->dispatch('close-modal', id: 'feedbackModal');
    }
}