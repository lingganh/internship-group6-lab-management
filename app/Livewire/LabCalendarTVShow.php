<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use App\Models\Lab;
use App\Models\EventCategory;
use App\Models\EventStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LabCalendarTVShow extends Component
{
    public $events = [];
    public $rooms = [];
    public $statuses = [];
    public $categories = [];

    public function mount()
    {
        $this->autoUpdateStatuses();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.lab-calendar-t-v-show')
            ->layout('components.layouts.client-layout-tv-show');
    }

    public function loadData()
    {
        $this->autoUpdateStatuses();

        $this->rooms = Lab::select('code', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();

        // Lấy danh sách trạng thái
        $this->statuses = EventStatus::select('id', 'code', 'name', 'color')
            ->where('code', '!=', 'cancelled')
            ->get()
            ->toArray();

        // Lấy danh sách loại sự kiện
        $this->categories = EventCategory::select('id', 'code', 'name', 'icon')
            ->get()
            ->toArray();

        $this->events = LabEvent::with(['lab:code,name', 'eventStatus', 'eventCategory'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('start')
            ->get()
            ->map(function ($event) {
                $now = Carbon::now();
                $start = Carbon::parse($event->start);
                $end = Carbon::parse($event->end);

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'category' => $event->category,
                    'category_icon' => $event->category_icon, // Icon từ EventCategory
                    'category_name' => $event->category_name, // Tên từ EventCategory
                    'status_name' => $event->status_name, // Tên từ EventStatus
                    'color' => $event->status_color, // Màu từ EventStatus
                    'lab_code' => $event->lab_code,
                    'lab_name' => $event->lab->name ?? '',
                    'start' => $event->start,
                    'end' => $event->end,
                    'status' => $event->status,
                    'description' => $event->description,
                    'registered_for' => $event->registered_for,
                    'is_current' => $now->between($start, $end),
                ];
            })
            ->toArray();
    }

    private function autoUpdateStatuses(): void
    {
        try {
            $now = Carbon::now();

            LabEvent::where('status', 'approved')
                ->where('end', '<', $now)
                ->update(['status' => 'completed']);

            LabEvent::where('status', 'pending')
                ->whereNotNull('start')
                ->where('start', '<=', $now)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => $now,
                ]);
        } catch (\Throwable $e) {
            Log::error('TV auto update error: ' . $e->getMessage());
        }
    }
}