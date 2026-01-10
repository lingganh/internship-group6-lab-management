<?php

namespace App\Livewire\Admin\equipment;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Lab;
use Illuminate\Support\Facades\DB;
use App\Models\EquipmentIssue;
use Livewire\WithPagination;
use App\Models\LabEquipmentItem;

class Edit extends Component
{
    public $equipmentId;
    public $lab_id;
    public $name;
    public $code;
    public $type;
    public $status = 'available';
    public $purchased_date;
    public $specifications = [];
    public $notes;
    public $quantity = 0;
    public $broken_quantity = 0;
    public $actual_quantity = 0;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';


    public function mount($id)
    {
        $eq = Equipment::with('labItems')->findOrFail($id);

        $this->equipmentId = $eq->id;
        $this->name = $eq->name;
        $this->code = $eq->code;
        $this->type = $eq->type;
        $this->status = $eq->status;
        $this->purchased_date = optional($eq->purchased_date)->format('Y-m-d');
        $this->notes = $eq->notes;

        $this->specifications = json_decode($eq->specifications, true) ?? [];

        $count = $eq->labItems->count();

        if ($count === 1) {
            $item = $eq->labItems->first();
            $this->lab_id = $item->lab_id;
            $this->quantity = (int) $item->quantity;
            $this->broken_quantity = (int) $item->broken_quantity;
            $this->actual_quantity = (int) $item->actual_quantity; // nếu cột có sẵn
        } else {
            $this->lab_id = null;
            $this->quantity = 0;
            $this->broken_quantity = 0;
            $this->actual_quantity = 0;
        }
    }


    protected function rules()
    {
        return [
            'lab_id' => 'required|exists:labs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:equipment,code,' . $this->equipmentId,
            'type' => 'required|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'purchased_date' => 'nullable|date',

            'quantity' => 'required|integer|min:0',
            'broken_quantity' => 'required|integer|min:0|max:' . $this->quantity,

            'specifications' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }


    public function update()
    {
        $this->validate();

        DB::transaction(function () {

            $equipment = Equipment::findOrFail($this->equipmentId);


            $equipment->update([
                'name' => $this->name,
                'code' => $this->code,
                'type' => $this->type,
                'status' => $this->status,
                'purchased_date' => $this->purchased_date,
                'notes' => $this->notes,
                'specifications' => json_encode($this->specifications),
            ]);


            $this->actual_quantity = max(0, $this->quantity - $this->broken_quantity);


            $equipment->labItems()->updateOrCreate(
                ['lab_id' => $this->lab_id],
                [
                    'quantity' => $this->quantity,
                    'broken_quantity' => $this->broken_quantity,
                    'actual_quantity' => $this->actual_quantity,
                ]
            );
        });

        // Thông báo
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Cập nhật thiết bị thành công!'
        ]);

        return redirect()->route('equipment.index');
    }

    public function render()
    {
        $issues = EquipmentIssue::with(['reporter', 'logs.changer'])
            ->where('equipment_id', $this->equipmentId)
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'issuesPage');

        $labItems = LabEquipmentItem::with('lab:id,name,code')
            ->where('equipment_id', $this->equipmentId)
            ->get();

        return view('livewire.admin.equipment.edit', [
            'labs'     => Lab::orderBy('name')->get(),
            'issues'   => $issues,
            'labItems' => $labItems,
        ])->layout('components.layouts.admin-layout');
    }
}
