<?php

namespace App\Livewire\Admin\Equipment;

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

    public function save(){
        $validate = $this->validate();

        Equipment::create($validate);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Thêm thiết bị thành công!'
        );

        return redirect()->route('admin.equipment.index');
    }
    public function render()
    {
        return view('livewire.admin.equipment.create',[
            'labs' => Lab::orderBy('name')->get(),
        ]);
    }
}
