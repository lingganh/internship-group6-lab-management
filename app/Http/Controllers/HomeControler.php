<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabEvent;
class HomeControler extends Controller
{
    public function eventsCalendar(Request $request){
        $query = LabEvent::query()->with('user');

         $upcomingEvents = (clone $query)
            ->where('start', '>=', now())
            ->where('category', 'seminar')  
            ->orderBy('start', 'asc')
            ->get();
 
        $pastQuery = (clone $query)
            ->where('start', '<', now())
            ->where('category', 'seminar');

         if ($request->has('year') && $request->year != '') {
            $pastQuery->whereYear('start', $request->year);
        }

        $pastEvents = $pastQuery->orderBy('start', 'desc')->paginate(10);  
         $years = LabEvent::selectRaw('YEAR(start) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        return view('pages.client.event-calendar',compact('upcomingEvents', 'pastEvents', 'years'));
    }

}
