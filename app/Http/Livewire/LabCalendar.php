<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LabCalendar extends Component
{
    public function render()
    {

        return view('livewire.lab-calendar')->layout('components.layouts.client-layout');
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
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập để đăng ký sự kiện.'
            ], 401);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'description' => 'nullable|string',
        ]);
        //        $event = new LabEvent($validate);

        $user = Auth::user();
        $isAdmin = $user->code === 'admin';
        $status = $isAdmin ? 'approved' : 'pending';

        $event = LabEvent::create([
            ...$validated,
            'user_id' => $user->id,
            'status' => $status,
        ]);


        return response()->json([
            'message' => $isAdmin
                ? 'Sự kiện đã được tạo và tự động duyệt.'
                : 'Đã gửi yêu cầu đăng ký. Vui lòng chờ quản trị viên phê duyệt.',
            'data' => $event,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
           'title'       => 'sometimes|string|max:255',
        'category'    => 'sometimes|string',
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
            'data' => $event,
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
