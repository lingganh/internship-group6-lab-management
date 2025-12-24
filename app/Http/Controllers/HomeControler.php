<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabEvent;

class HomeControler extends Controller
{
    public function eventsCalendar(Request $request)
    {
        $query = LabEvent::query()
            ->with(relations: 'user')
            ->where('status', '!=', 'cancelled');

        if ($request->filled('keyword')) {
            $query->where('title', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $upcomingEvents = (clone $query)
            ->where('start', '>=', now())
            ->orderBy('start', 'asc')
            ->get();

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
