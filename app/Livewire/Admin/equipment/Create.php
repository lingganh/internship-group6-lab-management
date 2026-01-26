<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\Lab;
use App\Models\LabEquipmentItem;

class Create extends Component
{
     // public $mode = 'existing';
    
    // Bỏ equipment_id vì không cần nữa
    // public $equipment_id;

    // new (master)
    public $name;
    public $code;
    public $type;
    public $status = 'available';
    public $specifications = [];
    public $notes;

    // pivot
    public $lab_id = 1;
    public $quantity = 0;
    public $broken_quantity = 0;
    public $actual_quantity = 0;

    public function mount()
    {
        $this->recalcActual();
    }

    public function updated($field)
    {
        if (in_array($field, ['quantity', 'broken_quantity'])) {
            $q = $this->quantity === "" ? 0 : (int)$this->quantity;
            $b = $this->broken_quantity === "" ? 0 : (int)$this->broken_quantity;
            
            $this->actual_quantity = max(0, $q - $b);
            $this->validateOnly($field);
        }
    }

    private function recalcActual()
    {
        $q = $this->quantity === "" ? 0 : (int)$this->quantity;
        $b = $this->broken_quantity === "" ? 0 : (int)$this->broken_quantity;
        $this->actual_quantity = max(0, $q - $b);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:equipment,code',
            'type' => 'required|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'quantity' => 'required|integer|min:0',
            'broken_quantity' => 'required|integer|min:0|lte:quantity',
            'notes' => 'nullable|string',
            'specifications' => 'nullable|array',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Tên thiết bị không được để trống.',
            'code.required' => 'Mã thiết bị không được để trống.',
            'code.unique' => 'Mã thiết bị này đã tồn tại trong hệ thống.',
            'type.required' => 'Vui lòng nhập loại thiết bị.',
            
            'quantity.required' => 'Số lượng tổng không được để trống.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập vào phải ít nhất là 0.',
            
            'broken_quantity.required' => 'Số lượng hỏng không được để trống.',
            'broken_quantity.integer' => 'Số lượng hỏng phải là số nguyên.',
            'broken_quantity.min' => 'Số lượng hỏng không được nhỏ hơn 0.',
            'broken_quantity.lte' => 'Số lượng hỏng không được vượt quá tổng số lượng đang có.',
        ];
    }

    public function save()
    {
        $this->validate();
        $this->recalcActual();

        $isMerged = false;

        DB::transaction(function () use (&$isMerged) {
            // 1. Tạo thiết bị mới
            $equipment = Equipment::create([
                'name' => $this->name,
                'code' => $this->code,
                'type' => $this->type,
                'status' => $this->status,
                'purchased_date' => now(),
                'notes' => $this->notes,
                'specifications' => json_encode($this->specifications),
            ]);
            
            $equipmentId = (int) $equipment->id;

            // 2. Kiểm tra trùng lặp và cộng dồn (lab_id = 1)
            $existingItem = LabEquipmentItem::where('lab_id', 1)
                ->where('equipment_id', $equipmentId)
                ->first();

            if ($existingItem) {
                // Trường hợp này không nên xảy ra vì equipment mới tạo
                // Nhưng giữ lại để an toàn
                $existingItem->update([
                    'quantity'        => $existingItem->quantity + (int) $this->quantity,
                    'broken_quantity' => $existingItem->broken_quantity + (int) $this->broken_quantity,
                    'actual_quantity' => $existingItem->actual_quantity + (int) $this->actual_quantity,
                ]);
                $isMerged = true;
            } else {
                // Tạo mới lab_equipment_item
                LabEquipmentItem::create([
                    'lab_id'          => 1,
                    'equipment_id'    => $equipmentId,
                    'quantity'        => (int) $this->quantity,
                    'broken_quantity' => (int) $this->broken_quantity,
                    'actual_quantity' => (int) $this->actual_quantity,
                ]);
            }
        });

        if ($isMerged) {
            $this->dispatch('alert', 
                type: 'info', 
                message: 'Thiết bị đã tồn tại trong phòng này. Hệ thống đã tự động cộng dồn số lượng mới!'
            );
        } else {
            $this->dispatch('alert', 
                type: 'success', 
                message: 'Đã thêm thiết bị mới thành công!'
            );
        }

        return redirect()->route('equipment.index');
    }

    public function render()
    {
        return view('livewire.admin.equipment.create', [
            'labs' => Lab::orderBy('name')->get(),
            // Bỏ equipments vì không cần nữa
            // 'equipments' => Equipment::orderBy('name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}