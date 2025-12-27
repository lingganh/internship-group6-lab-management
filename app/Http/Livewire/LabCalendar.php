<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LabEventFile;

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
         $booking = LabEvent::all();

        return response()->json($booking);
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
            'category'    => 'required|string',
            'lab_code'    => 'required|string|exists:labs,code',  
            'start'       => 'required|date',
            'end'         => 'required|date|after:start',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $isAdmin = $user->code === 'admin';
        $status = $isAdmin ? 'approved' : 'pending';

        $validated['status'] = $status;

        $event = LabEvent::create($validated);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('lab_files', 'public');

                LabEventFile::create([
                    'lab_event_id' => $event->id,
                    'file_name'    => $file->getClientOriginalName(),
                    'file_path'    => $path,
                    'file_type'    => $file->getClientMimeType(),
                    'file_size'    => $file->getSize(),
                ]);
            }
        }

        return response()->json([
            'message' => $isAdmin
                ? 'Sự kiện đã được tạo và tự động duyệt.'
                : 'Đã gửi yêu cầu đăng ký. Vui lòng chờ quản trị viên phê duyệt.',
            'data'    => $event,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'category'    => 'sometimes|string',
            'lab_code'    => 'sometimes|string|exists:labs,code',
            'start'       => 'required|date',
            'end'         => 'required|date|after:start',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user();
        $isAdmin = $user && $user->code === 'admin';

        $event = LabEvent::findOrFail($id);

        if (!$isAdmin && $event->status === 'approved') {
            $validated['status'] = 'pending';
        }

        $event->update($validated);

        return response()->json([
            'message' => 'Cập nhật sự kiện thành công.',
            'data'    => $event,
        ]);
    }

    public function destroy($id)
    {
        $event = LabEvent::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Đã xóa sự kiện thành công.'
        ], 200);
    }
}
