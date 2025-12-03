<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use Illuminate\Http\Request;

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
        $event = LabEvent::create($validated);

        return response()->json($event, 'Event đã tạo');
    }

    public function update()
    {

    }

    public function destroy($id)
    {
        $event = LabEvent::findOrFail($id);
        $event->delete();
        return response()->json(null,'đã xóa');
    }
}
