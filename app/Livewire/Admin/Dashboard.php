<?php

namespace App\Livewire\Admin;


use App\Models\LabEvent;
use Livewire\Component;


class Dashboard extends Component
{  

    public  $AllEvent;
    public  $ALLPendingEvt;
    public  $FirstEvent;

    public $WeekBCData;
    public $WeekPCData;
    public $MonthBCData;
    public $MonthPCData;

    public function mount(){
        //aboveData
        $this->AllEvent=LabEvent::query()->where('end','<=',now()->addDays(7))->where('start','>=',now())->count();
        $this->ALLPendingEvt=LabEvent::query()->where('end','<=',now()->addDays(7))->where('start','>=',now())->where('status','=','pending')->count();
        $this->FirstEvent=LabEvent::query()->where('status','=','pending')->orderBy('start', 'asc')->first();
        
        //
        $Week=LabEvent::query()->where('start','>=',now()->startOfWeek())->where('end','<=',now('UTC')->endOfWeek())->get();
        $Month=LabEvent::query()->where('start','>=',now()->startOfMonth())->where('end','<=',now('UTC')->endOfMonth())->get();
        $Year=LabEvent::query()->where('start','>=',now()->startOfYear())->where('end','<=',now('UTC')->endOfYear())->get();
   
        
        //chartData
        $BarData=[12, 19, 3, 5, 2, 3,12];
        
        

        //weekly data
        $days = collect();
        $start = now('UTC')->startOfWeek();
        $end   = now('UTC')->endOfWeek();

        while ($start <= $end) {
            $days->put($start->toDateString(), 0);
            $start->addDay();
        }

        //weekBarChart
        $this->WeekBCData = $days
        ->merge(
            collect($Week)->groupBy(fn ($e) =>$e->start->timezone('Asia/Ho_Chi_Minh')->toDateString())
            ->map->count()
         )
        ->map(fn ($count, $date) => [
            'date' => $date,
            'count' => $count,
        ])
        ->values();

        //weekPieChart  
        $this->WeekPCData=$Week
        ->groupBy('category')
        ->map(fn ($items, $category) => [
            'category' => ucfirst($category),
            'count'    => $items->count(),
        ])
        ->values();

        //Month data
        $monthStart = now('UTC')->startOfMonth();
        $monthEnd   = now('UTC')->endOfMonth();

         //Month barchart
        $this->MonthBCData = collect();
        $cursor = $monthStart->copy();
        $weekIndex = 1;

        while ($cursor <= $monthEnd) {
        $weekStart = $cursor->copy();
        $weekEnd   = $cursor->copy()->endOfWeek();

        if ($weekEnd > $monthEnd) {
            $weekEnd = $monthEnd->copy();
        }

        $count = $Month->filter(function ($e) use ($weekStart, $weekEnd) {
            return $e->start <= $weekEnd && $e->end >= $weekStart;
        })->count();

        $this->MonthBCData->push([
            'week' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
            'count' => $count,
        ]);

        $cursor = $weekEnd->addDay();
        $weekIndex++;
        }

        //Month PieChart
        $this->MonthPCData= $Month
        ->groupBy('category')
        ->map(fn ($items, $category) => [
        'category' => ucfirst($category),
        'count'    => $items->count(),
        ])
        ->values();
    }
   
    public function render()
    {
      
        
        $this->dispatch('push_data_weekbc', data: $this->WeekBCData);
        $this->dispatch('push_data_weekpc', data: $this->WeekPCData);
        return view('livewire.admin.dashboard');
    }
}
