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

class LabCalendar extends Component
{
    public function render()
    {
        $this->autoUpdateStatuses();

        $groups = Group::select('id', 'name') 
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
                    'id'             => $event->id,
                    'title'          => $event->title,
                    'category'       => $event->category,
                    'color'          => $event->color,
                    'lab_code'       => $event->lab_code,
                    'start'          => $event->start,
                    'end'            => $event->end,
                    'description'    => $event->description,
                    'registered_for' => $event->registered_for,
                    'status'         => $event->status,
                    'user_id'        => $event->user_id,
                ];
            });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $this->autoUpdateStatuses();

        if (!auth()->check()) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Bạn cần đăng nhập để đăng ký sự kiện.'
            ], 401);
        }

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|in:work,seminar,other',
            'color'          => 'nullable|string|max:20',
            'lab_code'       => 'required|string|exists:labs,code',
            'start'          => 'required|date',
            'end'            => 'required|date|after:start',
            'description'    => 'nullable|string|max:1000',
            'registered_for' => 'nullable|string|max:255',
        ], [
            'title.required'    => 'Vui lòng nhập tiêu đề sự kiện.',
            'lab_code.required' => 'Vui lòng chọn phòng lab.',
            'lab_code.exists'   => 'Phòng lab không tồn tại.',
            'start.required'    => 'Vui lòng chọn thời gian bắt đầu.',
            'end.required'      => 'Vui lòng chọn thời gian kết thúc.',
            'end.after'         => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $user = Auth::user();
        $isAdmin = $user->role_id == 1;
        $validated['status'] = $isAdmin ? 'approved' : 'pending';
        $validated['user_id'] = $user->id;
        $validated['updated_by'] = $user->id;

        $event = LabEvent::create($validated);

        if ($request->hasFile('files')) {
            $this->handleFileUploads($request->file('files'), $event->id);
        }

        $event->refresh();

        return response()->json([
            'message' => $isAdmin
                ? 'Sự kiện đã được tạo và tự động duyệt.'
                : 'Đã gửi yêu cầu đăng ký. Vui lòng chờ quản trị viên phê duyệt.',
            'data' => [
                'id'             => $event->id,
                'title'          => $event->title,
                'category'       => $event->category,
                'color'          => $event->color,
                'lab_code'       => $event->lab_code,
                'start'          => $event->start,
                'end'            => $event->end,
                'description'    => $event->description,
                'registered_for' => $event->registered_for,
                'status'         => $event->status,
                'user_id'        => $event->user_id,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $this->autoUpdateStatuses();

        $event = LabEvent::findOrFail($id);
        $user = auth()->user();
        $isAdmin = $user && $user->role_id ==1 ;

        // Kiểm tra quyền: Admin được sửa tất cả, user chỉ sửa event của mình
        if (!$isAdmin && $event->user_id !== $user->id) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Bạn không có quyền chỉnh sửa sự kiện này.'
            ], 403);
        }

        // Kiểm tra: Sự kiện đã duyệt và đã đến giờ thì không được sửa
        if ($event->status === 'approved' && Carbon::parse($event->start)->isPast()) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Không thể chỉnh sửa sự kiện đã duyệt và đã bắt đầu.'
            ], 403);
        }

        $validated = $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'category'       => 'sometimes|required|string|in:work,seminar,other',
            'color'          => 'nullable|string|max:20',
            'lab_code'       => 'sometimes|required|string|exists:labs,code',
            'start'          => 'required|date',
            'end'            => 'required|date|after:start',
            'description'    => 'nullable|string|max:1000',
            'registered_for' => 'nullable|string|max:255',
        ], [
            'lab_code.exists' => 'Phòng lab không tồn tại.',
            'end.after'       => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $validated['updated_by'] = $user ? $user->id : null;

        // Nếu không phải admin và sự kiện đang ở trạng thái approved, chuyển về pending
        if (!$isAdmin && $event->status === 'approved') {
            $validated['status'] = 'pending';
        }

        $event->update($validated);

        if ($request->hasFile('files')) {
            $this->handleFileUploads($request->file('files'), $event->id);
        }

        $event->refresh();

        return response()->json([
            'message' => !$isAdmin && $event->status === 'pending'
                ? 'Cập nhật thành công. Sự kiện đã chuyển về trạng thái chờ duyệt.'
                : 'Cập nhật sự kiện thành công.',
            'data' => [
                'id'             => $event->id,
                'title'          => $event->title,
                'category'       => $event->category,
                'color'          => $event->color,
                'lab_code'       => $event->lab_code,
                'start'          => $event->start,
                'end'            => $event->end,
                'description'    => $event->description,
                'registered_for' => $event->registered_for,
                'status'         => $event->status,
                'user_id'        => $event->user_id,
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
                'type'    => 'error',
                'message' => 'Bạn không có quyền xóa sự kiện này.'
            ], 403);
        }

        // Kiểm tra: Sự kiện đã duyệt và đã đến giờ thì không được xóa
        if ($event->status === 'approved' && Carbon::parse($event->start)->isPast()) {
            return response()->json([
                'type'    => 'error',
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
                    'file_name'    => $file->getClientOriginalName(),
                    'file_path'    => $path,
                    'file_type'    => $file->getClientMimeType(),
                    'file_size'    => $file->getSize(),
                ]);
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage());
            }
        }
    }
}