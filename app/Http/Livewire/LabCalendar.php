<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use App\Models\Lab;
use App\Models\LabEventFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use App\Enums\Role as RoleEnum;
use Illuminate\Support\Str;

class LabCalendar extends Component
{
    public function render()
    {
        $this->autoUpdateStatuses();

        $user = Auth::user();
        $groups = Group::select('id', 'name')
            ->when(!(!$user || (int) $user->role_id === 1), function ($q) use ($user) {
                $q->whereIn('id', function ($sub) use ($user) {
                    $sub->select('id')
                        ->from('lab_events')
                        ->where('leader_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();

        $rooms = Lab::select('code', 'name')
            ->orderBy('name')
            ->get();

        return view('livewire.lab-calendar', [
            'rooms' => $rooms,
            'groups' => $groups
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

    public function getAllBookings()
    {
        $this->autoUpdateStatuses();

        $events = LabEvent::with('lab:code,name')
            ->where('status', '!=', 'cancelled')
            ->orderBy('start')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'category' => $event->category,
                    'color' => $event->color,
                    'lab_code' => $event->lab_code,
                    'start' => $event->start,
                    'end' => $event->end,
                    'description' => $event->description,
                    'registered_for' => $event->registered_for,
                    'status' => $event->status,
                    'user_id' => $event->user_id,
                ];
            });

        return response()->json($events);
    }

    /**
     * CHECK TRÙNG LỊCH
     */
    private function hasConflict(string $labCode, $start, $end, $ignoreId = null): bool
    {
        return LabEvent::query()
            ->where('lab_code', $labCode)
            ->where('status', '!=', 'cancelled') // bỏ qua lịch đã hủy
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            // khoảng thời gian bị chồng lấn:
            // start_existing < end_new  &&  end_existing > start_new
            ->where('start', '<', $end)
            ->where('end', '>', $start)
            ->exists();
    }
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập.'
            ], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:work,seminar,other',
            'lab_code' => 'required|string|exists:labs,code',
            'description' => 'nullable|string|max:1000',
            'registered_for' => 'nullable|string|max:255',
            'occurrences' => 'nullable|array',
            'occurrences.*.start' => 'required_with:occurrences|date',
            'occurrences.*.end' => 'required_with:occurrences|date|after:occurrences.*.start',
        ]);

        $user = Auth::user();
        $isAdmin = (int) $user->role_id === 1;

        $seriesId = Str::uuid()->toString();
        $createdEvents = [];

        // nếu không có occurrences → tạo 1 lịch
        $occurrences = $request->input('occurrences');

        if (empty($occurrences)) {
            $occurrences = [
                [
                    'start' => $request->start,
                    'end' => $request->end,
                ]
            ];
        }

        foreach ($occurrences as $index => $occ) {
            // check trùng CHỈ với approved
            $conflict = LabEvent::where('lab_code', $request->lab_code)
                ->where('status', 'approved')
                ->where('start', '<', $occ['end'])
                ->where('end', '>', $occ['start'])
                ->exists();

            if ($conflict) {
                continue; // bỏ occurrence bị trùng
            }

            $event = LabEvent::create([
                'series_id' => count($occurrences) > 1 ? $seriesId : null,
                'title' => $request->title,
                'category' => $request->category,
                'color' => $request->color,
                'lab_code' => $request->lab_code,
                'start' => $occ['start'],
                'end' => $occ['end'],
                'description' => $request->description,
                'registered_for' => $request->registered_for,
                'status' => 'pending',
                'user_id' => $user->id,
                'updated_by' => $user->id,
            ]);

            // chỉ upload file cho event đầu
            if ($index === 0 && $request->hasFile('files')) {
                $this->handleFileUploads($request->file('files'), $event->id);
            }

            $createdEvents[] = $event;
        }

        if (empty($createdEvents)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Lịch bị trùng với lịch đã duyệt.'
            ], 409);
        }

        // notify admin 1 lần
        if (!$isAdmin) {
            $this->notifyAdminsPendingEvent(
                $createdEvents[0],
                'created',
                count($createdEvents)
            );
        }

        return response()->json([
            'messages' => 'Đã gửi yêu cầu đăng ký lịch.',
            'data' => [
                'created' => count($createdEvents),
                'series_id' => count($createdEvents) > 1 ? $seriesId : null,
            ]
        ], 201);
    }

    // public function store(Request $request)
    // {
    //     $this->autoUpdateStatuses();

    //     if (!auth()->check()) {
    //         return response()->json([
    //             'type' => 'error',
    //             'message' => 'Bạn cần đăng nhập để đăng ký sự kiện.'
    //         ], 401);
    //     }

    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'category' => 'required|string|in:work,seminar,other',
    //         'color' => 'nullable|string|max:20',
    //         // 'lab_code' => 'required|string|exists:labs,code',
    //         'start' => 'required|date',
    //         'end' => 'required|date|after:start',
    //         'description' => 'nullable|string|max:1000',
    //         'registered_for' => 'nullable|string|max:255',
    //     ], [
    //         'title.required' => 'Vui lòng nhập tiêu đề sự kiện.',
    //         'lab_code.required' => 'Vui lòng chọn phòng lab.',
    //         'lab_code.exists' => 'Phòng lab không tồn tại.',
    //         'start.required' => 'Vui lòng chọn thời gian bắt đầu.',
    //         'end.required' => 'Vui lòng chọn thời gian kết thúc.',
    //         'end.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
    //     ]);

    //     $validated['lab_code'] = 'LAB-304';
    //     // ====== CHECK TRÙNG TRƯỚC KHI TẠO ======
    //     if (
    //         $this->hasConflict(
    //             $validated['lab_code'],
    //             $validated['start'],
    //             $validated['end']
    //         )
    //     ) {
    //         return response()->json([
    //             'type' => 'error',
    //             'message' => 'Thời gian bạn chọn đang bị trùng với một lịch khác trong cùng phòng lab.'
    //         ], 409);
    //     }

    //     $user = Auth::user();
    //     $isAdmin = $user->role_id == 1;

    //     $validated['status'] = 'pending';
    //     $validated['user_id'] = $user->id;
    //     $validated['updated_by'] = $user->id;

    //     $event = LabEvent::create($validated);

    //     if ($request->hasFile('files')) {
    //         $this->handleFileUploads($request->file('files'), $event->id);
    //     }

    //     // CHỈ gửi thông báo nếu là occurrence đầu tiên của lịch lặp
    //     $isRecurring = $request->input('is_recurring') === 'true';
    //     $isFirstOccurrence = $request->input('is_first_occurrence') === 'true';
    //     $totalOccurrences = (int) $request->input('total_occurrences', 1);

    //     if (!$isAdmin && (!$isRecurring || $isFirstOccurrence)) {
    //         $this->notifyAdminsPendingEvent($event, 'created', $totalOccurrences);
    //     }
    //     $event->refresh();

    //     return response()->json([
    //         'message' => $isAdmin
    //             ? 'Sự kiện đã được tạo và tự động duyệt.'
    //             : 'Đã gửi yêu cầu đăng ký. Vui lòng chờ quản trị viên phê duyệt.',
    //         'data' => [
    //             'id' => $event->id,
    //             'title' => $event->title,
    //             'category' => $event->category,
    //             'color' => $event->color,
    //             'lab_code' => $event->lab_code,
    //             'start' => $event->start,
    //             'end' => $event->end,
    //             'description' => $event->description,
    //             'registered_for' => $event->registered_for,
    //             'status' => $event->status,
    //             'user_id' => $event->user_id,
    //         ],
    //     ], 201);
    // }

    public function update(Request $request, $id)
    {
        $this->autoUpdateStatuses();

        $event = LabEvent::findOrFail($id);
        $user = auth()->user();
        $isAdmin = $user && $user->role_id == 1;

        // Kiểm tra quyền: Admin được sửa tất cả, user chỉ sửa event của mình
        if (!$isAdmin && $event->user_id !== $user->id) {
            return response()->json([
                'type' => 'error',
                'message' => 'Bạn không có quyền chỉnh sửa sự kiện này.'
            ], 403);
        }

        // Sự kiện đã duyệt và đã đến giờ thì không được sửa
        if ($event->status === 'approved' && Carbon::parse($event->start)->isPast()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Không thể chỉnh sửa sự kiện đã duyệt và đã bắt đầu.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|in:work,seminar,other',
            'color' => 'nullable|string|max:20',
            'lab_code' => 'sometimes|required|string|exists:labs,code',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'description' => 'nullable|string|max:1000',
            'registered_for' => 'nullable|string|max:255',
        ], [
            'lab_code.exists' => 'Phòng lab không tồn tại.',
            'end.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $validated['updated_by'] = $user ? $user->id : null;

        // dữ liệu dùng để check trùng (nếu không gửi lab_code mới thì dùng lab cũ)
        $labCode = $validated['lab_code'] ?? $event->lab_code;
        $start = $validated['start'] ?? $event->start;
        $end = $validated['end'] ?? $event->end;

        // ====== CHECK TRÙNG TRƯỚC KHI UPDATE ======
        if ($this->hasConflict($labCode, $start, $end, $event->id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'Thời gian bạn chọn đang bị trùng với một lịch khác trong cùng phòng lab.'
            ], 409);
        }

        // Nếu không phải admin và sự kiện đang ở trạng thái approved, chuyển về pending
        if (!$isAdmin && $event->status === 'approved') {
            $validated['status'] = 'pending';
        }

        $event->update($validated);

        if ($request->hasFile('files')) {
            $this->handleFileUploads($request->file('files'), $event->id);
        }
        if (!$isAdmin && $event->status === 'pending') {
            $this->notifyAdminsPendingEvent($event, 'updated');
        }
        $event->refresh();

        return response()->json([
            'message' => (!$isAdmin && $event->status === 'pending')
                ? 'Cập nhật thành công. Sự kiện đã chuyển về trạng thái chờ duyệt.'
                : 'Cập nhật sự kiện thành công.',
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $event->category,
                'color' => $event->color,
                'lab_code' => $event->lab_code,
                'start' => $event->start,
                'end' => $event->end,
                'description' => $event->description,
                'registered_for' => $event->registered_for,
                'status' => $event->status,
                'user_id' => $event->user_id,
            ],
        ]);
    }

    public function destroy($id)
    {
        $event = LabEvent::findOrFail($id);
        $user = auth()->user();
        $isAdmin = $user && $user->code === 'admin';

        // Kiểm tra quyền: Admin được xóa tất cả, user chỉ xóa event của mình
        if (!$isAdmin && $event->user_id !== $user->id) {
            return response()->json([
                'type' => 'error',
                'message' => 'Bạn không có quyền xóa sự kiện này.'
            ], 403);
        }

        // Sự kiện đã duyệt và đã đến giờ thì không được xóa
        if ($event->status === 'approved' && Carbon::parse($event->start)->isPast()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Không thể xóa sự kiện đã duyệt và đã bắt đầu.'
            ], 403);
        }

        $files = LabEventFile::where('lab_event_id', $id)->get();
        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . $file->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }
        $event->delete();
        if ($event->status === 'approved') {
            $this->notifyAdminsDeletedEvent($event);
        }



        return response()->json([
            'message' => 'Đã xóa sự kiện thành công.'
        ], 200);
    }

    private function handleFileUploads($files, $eventId)
    {
        foreach ($files as $file) {
            try {
                $path = $file->store('lab_files', 'public');

                LabEventFile::create([
                    'lab_event_id' => $eventId,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
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

        // Tìm tất cả User có role_id là 1 (Admin)
        $admins = User::where('role_id', 1)->get();

        if ($admins->isEmpty())
            return;

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
            if ($admin->email) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->queue(
                    new \App\Mail\AdminPendingEventNotification($event, $senderName, $action)
                );
            }
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

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Lịch đặt phòng đã bị hủy',
                'message' => "{$senderName} đã hủy lịch: {$event->title} tại phòng {$event->lab_code}",
                'data' => [
                    'event_id' => $event->id,
                    'type' => 'deleted_event',
                    'sender_name' => $senderName,
                    'url' => route('admin.approval'),
                ],
            ]);

            if ($admin->email) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->queue(
                    new \App\Mail\AdminDeletedEventNotification($event, $senderName)
                );
            }
        }
    }

}
