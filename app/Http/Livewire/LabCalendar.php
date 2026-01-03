<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use App\Models\Lab;
use App\Models\LabEventFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LabCalendar extends Component
{
    public function render()
    {
        $rooms = Lab::select('code', 'name')
            ->orderBy('name')
            ->get();

        return view('livewire.lab-calendar', [
            'rooms' => $rooms,
        ])->layout('components.layouts.client-layout');
    }

    public function getAllBookings()
    {
        $events = LabEvent::with('lab:code,name')
            ->orderBy('start')
            ->get()
            ->map(function ($event) {
                return [
                    'id'          => $event->id,
                    'title'       => $event->title,
                    'category'    => $event->category,
                    'color'       => $event->color,
                    'lab_code'    => $event->lab_code,
                    'start'       => $event->start,
                    'end'         => $event->end,
                    'description' => $event->description,
                    'status'      => $event->status,
                ];
            });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Bạn cần đăng nhập để đăng ký sự kiện.'
            ], 401);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|in:work,seminar,other',
            'color'       => 'nullable|string|max:20',
            'lab_code'    => 'required|string|exists:labs,code',
            'start'       => 'required|date',
            'end'         => 'required|date|after:start',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required'    => 'Vui lòng nhập tiêu đề sự kiện.',
            'lab_code.required' => 'Vui lòng chọn phòng lab.',
            'lab_code.exists'   => 'Phòng lab không tồn tại.',
            'start.required'    => 'Vui lòng chọn thời gian bắt đầu.',
            'end.required'      => 'Vui lòng chọn thời gian kết thúc.',
            'end.after'         => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            
        ]);

        $user = Auth::user();
        $isAdmin = $user->code === 'admin';
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
                'id'          => $event->id,
                'title'       => $event->title,
                'category'    => $event->category,
                'color'       => $event->color,
                'lab_code'    => $event->lab_code,
                'start'       => $event->start,
                'end'         => $event->end,
                'description' => $event->description,
                'status'      => $event->status,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $event = LabEvent::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'category'    => 'sometimes|required|string|in:work,seminar,other',
            'color'       => 'nullable|string|max:20',
            'lab_code'    => 'sometimes|required|string|exists:labs,code',
            'start'       => 'required|date',
            'end'         => 'required|date|after:start',
            'description' => 'nullable|string|max:1000',
        ], [
            'lab_code.exists' => 'Phòng lab không tồn tại.',
            'end.after'       => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $user = auth()->user();
        $isAdmin = $user && $user->code === 'admin';
        $validated['updated_by'] = $user->id;
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
                'id'          => $event->id,
                'title'       => $event->title,
                'category'    => $event->category,
                'color'       => $event->color,
                'lab_code'    => $event->lab_code,
                'start'       => $event->start,
                'end'         => $event->end,
                'description' => $event->description,
                'status'      => $event->status,
            ],
        ]);
    }

    public function destroy($id)
    {
        $event = LabEvent::findOrFail($id);

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