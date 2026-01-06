<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Lab;

class Create extends Component
{

    public $name;
    public $code;
    public $type;
    public $status = 'available';
    public $purchased_date;
    public $specifications = [];
    public $notes;


    public $lab_id;
    public $quantity = 0;
    public $broken_quantity = 0;
    public $actual_quantity = 0;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:equipment,code',
            'type' => 'required|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'purchased_date' => 'nullable|date',
            'specifications' => 'nullable|array',
            'notes' => 'nullable|string',
            'lab_id' => 'required|exists:labs,id',
            'quantity' => 'required|integer|min:0',
            'broken_quantity' => 'required|integer|min:0|lte:quantity',
        ];
    }



    public function save()
    {
        $validated = $this->validate();


        $equipment = Equipment::create([
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'status' => $this->status,
            'purchased_date' => $this->purchased_date,
            'notes' => $this->notes,
            'specifications' => json_encode($this->specifications),
        ]);


        $equipment->labItems()->create([
            'lab_id' => $this->lab_id,
            'quantity' => $this->quantity,
            'broken_quantity' => $this->broken_quantity,

        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Thêm thiết bị thành công!'
        ]);

        return redirect()->route('equipment.index');
    }

    public function render()
    {
        return view('livewire.admin.equipment.create', [
            'labs' => Lab::orderBy('name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}
