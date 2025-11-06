<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LabEvent;
use Carbon\Carbon;

class LabCalendar extends Component
{



    public function render()
    {

        return view('livewire.lab-calendar')->layout('components.layouts.client-layout');
    }
    public function getAllBookings(){
        $booking=LabEvent::all();
        return response()->json($booking);
    }

}
