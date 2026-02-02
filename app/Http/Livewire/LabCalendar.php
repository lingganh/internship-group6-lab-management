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
use Illuminate\Support\Facades\Storage;
use function in_array;
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

    $events = LabEvent::with(['lab:code,name', 'eventStatus', 'eventCategory'])
        ->where('status', '!=', 'cancelled')
        ->orderBy('start')
        ->get()
        ->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $event->category,
                'color' => $event->status_color, // Lấy màu từ EventStatus
                'category_icon' => $event->category_icon, // Lấy icon từ EventCategory
                'category_name' => $event->category_name, // Lấy tên category
                'status_name' => $event->status_name, // Lấy tên status
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
        return response()->json(['type' => 'error', 'message' => 'Bạn cần đăng nhập.'], 401);
    }
    
    $user = Auth::user();

    // 1. Kiểm tra quyền (Giữ nguyên)
    if (!in_array($user->role?->name, [
        RoleEnum::Admin->value,
        RoleEnum::Officer->value,
        RoleEnum::Teacher->value,
    ], true)) {
        return response()->json(['type' => 'error', 'message' => 'Bạn không có quyền đăng ký.'], 403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'required|string|in:work,seminar,other',
        'lab_code' => 'required|string',
        'occurrences' => 'required|array|min:1',
        'force' => 'nullable'
    ]);

 
    $occurrences = $request->input('occurrences');
    $isSingleEvent = count($occurrences) === 1;
    $force = $request->input('force') === 'true' || $request->has('force');
    
    \Log::info('=== STORE START ===', [
        'force' => $force,
        'occurrences_count' => count($occurrences)
    ]);
    
    $seriesId = !$isSingleEvent ? Str::uuid()->toString() : null;
    $createdEvents = [];
    $conflicts = [];

     foreach ($occurrences as $index => $occ) {
        $conflictedEvent = LabEvent::where('lab_code', $request->lab_code)
            ->where('status', 'approved')
            ->where('start', '<', $occ['end'])
            ->where('end', '>', $occ['start'])
            ->first();

        if ($conflictedEvent) {
            $formattedConflict = [
                'requested_start' => Carbon::parse($occ['start'])->format('d/m/Y H:i'),
                'requested_end' => Carbon::parse($occ['end'])->format('d/m/Y H:i'),
                'conflict_with' => [
                    'title' => $conflictedEvent->title,
                    'start' => Carbon::parse($conflictedEvent->start)->format('d/m/Y H:i'),
                    'end' => Carbon::parse($conflictedEvent->end)->format('d/m/Y H:i')
                ]
            ];

             if ($isSingleEvent) {
                return response()->json([
                    'type' => 'error',
                    'message' => "Không thể đăng ký lịch. Lịch đăng ký trùng với: {$conflictedEvent->title}",
                    'data' => ['conflict' => $formattedConflict]
                ], 409);
            }

            $conflicts[] = $formattedConflict;
        }
    }

    \Log::info('Check conflicts done', [
        'conflicts_count' => count($conflicts),
        'force' => $force
    ]);

     if (!empty($conflicts) && !$force) {
        \Log::info('Has conflicts and no force → return confirm dialog (NOT creating anything)');
        return response()->json([
            'type' => 'confirm',
            'message' => 'Một số buổi trong chuỗi bị trùng lịch. Bạn có muốn tiếp tục đăng ký các buổi còn lại không?',
            'data' => ['conflicts' => $conflicts]
        ]);
    }

     \Log::info('Creating events...');
    
    foreach ($occurrences as $index => $occ) {
         $hasConflict = LabEvent::where('lab_code', $request->lab_code)
            ->where('status', 'approved')
            ->where('start', '<', $occ['end'])
            ->where('end', '>', $occ['start'])
            ->exists();

        if ($hasConflict) {
            \Log::info("Skip occurrence #{$index} (has conflict)");
            continue;  
        }

        \Log::info("Creating occurrence #{$index}");
        
        $event = LabEvent::create([
            'series_id' => $seriesId,
            'title' => $request->title,
            'category' => $request->category,
            'lab_code' => $request->lab_code,
            'start' => $occ['start'],
            'end' => $occ['end'],
            'description' => $request->description,
            'registered_for' => $request->registered_for,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);
        
        $createdEvents[] = $event;
    }

    \Log::info('Events created', ['count' => count($createdEvents)]);

   if (empty($createdEvents)) {
        return response()->json([
            'type' => 'error', 
            'message' => 'Tất cả các lịch đăng ký đều bị trùng.'
        ], 409);
    }

    // File upload & notification...
    if ($request->hasFile('files')) {
        foreach ($createdEvents as $event) {
            $this->handleFileUploads($request->file('files'), $event->id);
        }
    }

    if (auth()->user()->role_id != 1) {
        $this->notifyAdminsPendingEvent($createdEvents[0], 'created', count($createdEvents));
    }

    return response()->json([
        'type' => 'success',
        'message' => 'Đã gửi yêu cầu đăng ký thành công (' . count($createdEvents) . ' lịch).',
    ], 201);
}
//             if (!$force) continue;
//             continue; 
//         }

//         $event = LabEvent::create([
//             'series_id' => count($occurrences) > 1 ? $seriesId : null,
//             'title' => $request->title,
//             'category' => $request->category,
//             'lab_code' => $request->lab_code,
//             'start' => $occ['start'],
//             'end' => $occ['end'],
//             'description' => $request->description,
//             'registered_for' => $request->registered_for,
//             'status' => 'pending',
//             'user_id' => $user->id,
//         ]);
//         $createdEvents[] = $event;
//     }

//     if (!$force && !empty($conflicts)) {
//         return response()->json([
//             'type' => 'confirm',
//             'message' => 'Phát hiện trùng lịch.',
//             'data' => ['conflicts' => $conflicts]
//         ]);
//     }

//     if (empty($createdEvents)) {
//         return response()->json([
//             'type' => 'error', 
//             'message' => 'Không có lịch nào hợp lệ để tạo.'
//         ], 409);
//     }

//      if ($request->hasFile('files')) {
//         foreach ($createdEvents as $event) {
//             $this->handleFileUploads($request->file('files'), $event->id);
//         }
//     }

//     if ($user->role_id != 1) {
//         $this->notifyAdminsPendingEvent($createdEvents[0], 'created', count($createdEvents));
//     }

//     return response()->json([
//         'type' => 'success',
//         'message' => 'Đã gửi yêu cầu đăng ký thành công (' . count($createdEvents) . ' lịch).',
//     ], 201);
// }


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
    try {
        $event = LabEvent::findOrFail($id);
        $user = auth()->user();
        $isAdmin = $user && $user->code === 'admin';

         if (!$isAdmin && $event->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa sự kiện này.'
            ], 403);
        }

         if ($event->status === 'approved' && Carbon::parse($event->start)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sự kiện đã duyệt và đã bắt đầu.'
            ], 403);
        }

         try {
            $files = LabEventFile::where('lab_event_id', $id)->get();
            foreach ($files as $file) {
                try {
                    if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }
                    $file->delete();
                } catch (\Throwable $e) {
                    \Log::warning("Không thể xóa file {$file->id}: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            \Log::error("Lỗi khi xóa files cho event {$id}: " . $e->getMessage());
        }

        $wasApproved = $event->status === 'approved' ;
        
         if ($wasApproved) {
            $event->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);
            
            $message = 'Lịch đã duyệt đã được chuyển sang trạng thái hủy.';
            $action = 'cancelled';
        } else if($event->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sự kiện đã hoàn thành.'
            ], 403);

        }
         else {
             $event->delete();
            
            $message = 'Đã xóa sự kiện thành công.';
            $action = 'deleted';
        }

         if ($wasApproved) {
            try {
                $this->notifyAdminsDeletedEvent($event);
            } catch (\Throwable $e) {
                \Log::error("Lỗi khi thông báo admin: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'action' => $action // ← Cho frontend biết đã làm gì
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy sự kiện.'
        ], 404);
        
    } catch (\Throwable $e) {
        \Log::error("Lỗi xóa event {$id}: " . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi xóa sự kiện.'
        ], 500);
    }
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

        if ($admin->email) {
            \Illuminate\Support\Facades\Mail::to($admin->email)->queue(
                new \App\Mail\AdminDeletedEventNotification($event, $senderName)
            );
        }
    }
}

}
