<?php

namespace App\Livewire\Admin\equipment;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Lab;

class Create extends Component
{

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

    protected function rules()
    {
        return [
            'lab_id' => 'required|exists:labs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:equipment,code',
            'type' => 'required|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'purchased_date' => 'nullable|date',
            'specifications' => 'nullable|array',
            'notes' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'broken_quantity' => 'required|integer|min:0',
            'actual_quantity' => 'required|integer|min:0',
        ];
    }

    public function save(){
        $validated = $this->validate();

        Equipment::create([
            ...$validated,
            'quantity' => $this->quantity,
            'broken_quantity' => $this->broken_quantity,
            'actual_quantity' => $this->actual_quantity,
        ]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Thêm thiết bị thành công!'
        );

        return redirect()->route('equipment.index');
    }
    public function render()
    {
        return view('livewire.admin.equipment.create',[
            'labs' => Lab::orderBy('name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}
