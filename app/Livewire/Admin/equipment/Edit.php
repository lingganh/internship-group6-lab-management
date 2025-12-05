<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Lab;

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


    public function mount($id)
    {
        $this->equipmentId = $id;
        $eq = Equipment::findOrFail($id);

        $this->lab_id = $eq->lab_id;
        $this->name = $eq->name;
        $this->code = $eq->code;
        $this->type = $eq->type;
        $this->status = $eq->status;
        $this->purchased_date = $eq->purchased_date;
        $this->specifications = $eq->specifications ?? [];
        $this->notes = $eq->notes ;
    }
    protected function rules()
    {
        return [
            'lab_id' => 'required|exists:labs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:equipments,code',
            'type' => 'required|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'purchased_date' => 'nullable|date',
            'specifications' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }

    public function update(){
        $data = $this->validate();

        Equipment::findOrFail($this->equipmentId)->update($data);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Cập nhật thiết bị thành công!'
        );

        return redirect()->route('admin.equipment.index');
    }
    public function render()
    {
        return view('livewire.admin.equipment.edit',[
            'labs' => Lab::orderBy('name')->get(),
        ]);
    }
}
