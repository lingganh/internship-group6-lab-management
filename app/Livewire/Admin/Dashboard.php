<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\Lab;
use App\Models\LabEquipmentItem;
use App\Models\LabEvent;
use Livewire\Component;



class Dashboard extends Component
{

    public  $AllEvent;
    public  $ALLPendingEvt;
    public  $FirstEvent;
    public  $FaultyEquip;
    public  $EuqipNum;
    public  $MaintaceEquip;

    public $WeekBCData;
    public $WeekPCData;
    public $MonthBCData;
    public $MonthPCData;
    public $AllPCData;
    public $EquipCData;
    public $WeekLCData;
    public $MonthLCData;
    public $TopEvent;
    public $Temp;

    public function mount()
    {
        //aboveData
        $this->AllEvent = LabEvent::query()->where('start', '>=', now())->where('status', '=', 'approved')->count();
        $this->ALLPendingEvt = LabEvent::query()->where('status', '=', 'pending')->count();
        $this->FirstEvent = LabEvent::query()->where('start', '>=', now())->where('status', '=', 'pending')->orderBy('start', 'asc')->first();
        $this->TopEvent = $this->TopEvent = LabEvent::where('status', 'approved')
            ->orderBy('start', 'asc')
            ->limit(5)
            ->get();

        $this->FaultyEquip  = LabEquipmentItem::sum('broken_quantity');;
        $this->EuqipNum = LabEquipmentItem::sum('quantity');
        $this->MaintaceEquip = LabEquipmentItem::whereHas('equipment', function ($q) {
            $q->where('status', 'maintenance');
        })
            ->sum('quantity');
        $this->EquipCData = [
            [
                'status' => 'Available',
                'count'  => $this->EuqipNum-$this->MaintaceEquip-$this->FaultyEquip ,
            ],
            [
                'status' => 'Maintenance',
                'count'  => $this->MaintaceEquip,
            ],
            [
                'status' => 'Broken',
                'count'  => $this->FaultyEquip ,
            ],
        ];

        //
        $Week = LabEvent::query()->where('start', '>=', now()->startOfWeek())->where('end', '<=', now('UTC')->endOfWeek())->get();
        $Month = LabEvent::query()->where('start', '>=', now()->startOfMonth())->where('end', '<=', now('UTC')->endOfMonth())->get();
        $Year = LabEvent::query()->where('start', '>=', now()->startOfYear())->where('end', '<=', now('UTC')->endOfYear())->get();
        $All = LabEvent::query()->get();

        //chartData
        $BarData = [12, 19, 3, 5, 2, 3, 12];



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
                collect($Week)->groupBy(fn($e) => $e->start->timezone('Asia/Ho_Chi_Minh')->toDateString())
                    ->map->count()
            )
            ->map(fn($count, $date) => [
                'date' => $date,
                'count' => $count,
            ])
            ->values();

        //weekPieChart  
        $this->WeekPCData = $Week
            ->groupBy('category')
            ->map(fn($items, $category) => [
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
                'date' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'count' => $count,
            ]);

            $cursor = $weekEnd->addDay();
            $weekIndex++;
        }

        //Month PieChart
        $this->MonthPCData = $Month
            ->groupBy('category')
            ->map(fn($items, $category) => [
                'category' => ucfirst($category),
                'count'    => $items->count(),
            ])
            ->values();


        $this->AllPCData = $All
            ->groupBy('category')
            ->map(fn($items, $category) => [
                'category' => ucfirst($category),
                'count'    => $items->count(),
            ])
            ->values();


        $this->dispatch('create_chart');
        $this->dispatch('push_data_weekbc', data: $this->WeekBCData);
        $this->dispatch('push_data_weekpc', data: $this->WeekPCData);
        $this->dispatch('push_data_equip', data: $this->EquipCData);
    }

    public function loadBarWeek()
    {
        $this->dispatch('push_data_weekbc', data: $this->WeekBCData);
    }
    public function loadBarMonth()
    {
        $this->dispatch('push_data_monthbc', data: $this->MonthBCData);
    }
    public function loadPieWeek()
    {
        $this->dispatch('push_data_weekpc', data: $this->WeekPCData);
    }
    public function loadPieMonth()
    {
        $this->dispatch('push_data_monthpc', data: $this->MonthPCData);
    }
    public function loadPieAll()
    {
        $this->dispatch('push_data_allpc', data: $this->AllPCData);
    }


    public function render()
    {
        return view('livewire.admin.dashboard');
    }

   

    
}
