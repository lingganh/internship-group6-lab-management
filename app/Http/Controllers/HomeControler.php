<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabEvent;
use Livewire\WithPagination;

class HomeControler extends Controller
{
    use WithPagination;
    
    public function eventsCalendar(Request $request)
    {
        $query = LabEvent::query()
            ->with(['user:id,full_name', 'lab:code,name', 'files'])
            ->where('status', 'approved');

        if ($request->filled('keyword')) {
            $query->where('title', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $upcomingEvents = (clone $query)
            ->where('start', '>=', now())
            ->orderByDesc('is_featured')
            ->orderBy('start', 'asc')
            ->paginate(10);

        $pastQuery = (clone $query)
            ->where('start', '<', now());

        if ($request->filled('year')) {
            $pastQuery->whereYear('start', $request->year);
        }

        $pastEvents = $pastQuery
            ->orderBy('start', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $years = LabEvent::selectRaw('YEAR(start) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $categories = LabEvent::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view(
            'pages.client.event-calendar',
            compact('upcomingEvents', 'pastEvents', 'years', 'categories')
        );
    }
    
}