<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\Lab;
use App\Models\LabEquipmentItem;
use App\Models\LabEvent;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;


class Report extends Component
{
    public $LabList;
    public $selectedLab = 'all';
    public $fromDate = '';
    public $toDate = '';
    public $EquipList;

    public $exportLab;
    public $exportEquip;

    public function mount()
    {
        // Load all labs for the filter dropdown
        $this->LabList = Lab::all();
        $this->EquipList = Equipment::all();
        $this->dispatch('create_chart');
    }

    public function render()
    {
        // Start query for LabEvent
        $query = LabEvent::query();


        // Filter by selected lab
        if ($this->selectedLab !== 'all') {
            $lab = Lab::find($this->selectedLab);
            if ($lab) {
                $query->where('lab_code', $lab->code);


                $chartData1 =  LabEquipmentItem::selectRaw('
                equipment.status,
                SUM(lab_equipment_items.quantity) as count')
                    ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                    ->where('lab_id', $lab->id)
                    ->groupBy('equipment.status')
                    ->get();

                $chartData2 = LabEquipmentItem::selectRaw('
                equipment.type,
                SUM(lab_equipment_items.quantity) as count')
                    ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                    ->where('lab_id', $lab->id)
                    ->groupBy('equipment.type')
                    ->get();
            }
        } else {
            $chartData1 =  LabEquipmentItem::selectRaw('
        equipment.status,
        SUM(lab_equipment_items.quantity) as count')
                ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                ->groupBy('equipment.status')
                ->get();

            $chartData2 = LabEquipmentItem::selectRaw('
        equipment.type,
        SUM(lab_equipment_items.quantity) as count')
                ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                ->groupBy('equipment.type')
                ->get();
        }


        if ($this->fromDate !== '') {
            $query->whereDate('start', '>=', $this->fromDate);
        }

        if ($this->toDate !== '') {
            $query->whereDate('end', '<=', $this->toDate);
        }

        // Fetch events
        $events = $query->orderBy('start', 'desc')->limit(5)->get();
        $this->exportLab = $query->orderBy('start', 'desc')->get();






        $this->dispatch('push_PCData1', data: $chartData1);
        $this->dispatch('push_PCData2', data: $chartData2);

        return view('livewire.admin.report', [  
            'events' => $events,
            'chartData1' => $chartData1,
            'chartData2' => $chartData2,
        ]);
    }

    public function exportPdf()
    {

        $summary = [
            'total'     => $this->exportLab->count(),
            'approved'  => $this->exportLab->where('status', 'approved')->count(),
            'pending'   => $this->exportLab->where('status', 'pending')->count(),
            'completed' => $this->exportLab->where('status', 'completed')->count(),
        ];

        if ($this->selectedLab !== 'all') {
            $lab = Lab::find($this->selectedLab);
            if ($lab) {
                $equipmentStats = LabEquipmentItem::selectRaw('
                     equipment.status,
                    SUM(lab_equipment_items.quantity) as count')
                    ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                    ->groupBy('equipment.status')
                    ->where('lab_id', $lab->id)
                    ->get();

                $equipmentStats =  Equipment::query()
                    ->leftJoin('lab_equipment_items', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                    ->selectRaw('COALESCE(SUM(lab_equipment_items.actual_quantity), 0) as total')
                    ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'available' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as available")
                    ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'maintenance' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as maintenance")
                    ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'broken' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as broken")
                    ->where('lab_id', $lab->id)
                    ->first();

                $rows = LabEquipmentItem::with([
                    'equipment:id,name,code,type,status,purchased_date,specifications,notes,created_at,updated_at'
                ])
                    ->select(
                        'id',
                        'lab_id',
                        'equipment_id',
                        'actual_quantity',
                        'broken_quantity'
                    )
                    ->where('lab_id', $lab->id)
                    ->get();
            }
        } else {
            $equipmentStats = LabEquipmentItem::selectRaw('
                equipment.status,
                SUM(lab_equipment_items.quantity) as count')
                ->join('equipment', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                ->groupBy('equipment.status')
                ->get();

            $equipmentStats =  Equipment::query()
                ->leftJoin('lab_equipment_items', 'equipment.id', '=', 'lab_equipment_items.equipment_id')
                ->selectRaw('COALESCE(SUM(lab_equipment_items.actual_quantity), 0) as total')
                ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'available' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as available")
                ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'maintenance' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as maintenance")
                ->selectRaw("COALESCE(SUM(CASE WHEN equipment.status = 'broken' THEN lab_equipment_items.actual_quantity ELSE 0 END), 0) as broken")
                ->first();

            $rows = LabEquipmentItem::with([
                'equipment:id,name,code,type,status,purchased_date,specifications,notes,created_at,updated_at',
                'lab:id,name'
            ])
                ->select(
                    'id',
                    'lab_id',
                    'equipment_id',
                    'actual_quantity',
                    'broken_quantity'
                )
                ->get();
        }



        $equipmentsByType = $rows
            ->groupBy(fn($item) => $item->equipment->type)
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'id'              => $item->equipment->id,
                        'name'            => $item->equipment->name,
                        'code'            => $item->equipment->code,
                        'type'            => $item->equipment->type,
                        'status'          => $item->equipment->status,
                        'purchased_date'  => $item->equipment->purchased_date,
                        'specifications'  => $item->equipment->specifications,
                        'notes'           => $item->equipment->notes,
                        'created_at'      => $item->equipment->created_at,
                        'updated_at'      => $item->equipment->updated_at,
                        'lab_id'          => $item->lab_id,
                        'lab_name'        => $item->lab->name,
                        'actual_quantity' => $item->actual_quantity,
                        'broken_quantity' =>  $item->broken_quantity,
                    ];
                })->values();
            });

        $groupedEvents = $this->exportLab->groupBy('lab_code');

        $pdf = Pdf::loadView('pdf.lab-report', [
            'groupedEvents' => $groupedEvents,
            'summary'      => $summary,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'lab' => $this->selectedLab,
            'equipmentStats'    => $equipmentStats,
            'equipmentsByType'  => $equipmentsByType,
        ])->setPaper('a4', 'landscape');


        return response()->streamDownload(
            fn() => print($pdf->output()),
            'bao-cao-phong-lab-' . now()->format('d-m-Y') . '.pdf'
        );
    }
}
