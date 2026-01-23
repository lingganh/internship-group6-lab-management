<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\Lab;
use App\Models\LabEquipmentItem;

class Create extends Component
{
    public $mode = 'existing'; // existing | new

    // existing
    public $equipment_id;

    // new (master)
    public $name;
    public $code;
    public $type;
    public $status = 'available';
    public $specifications = [];
    public $notes;

    // pivot
    public $lab_id;
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
            // Xử lý biến tạm để tránh ép kiểu về 0 ngay lập tức khi user xóa trắng input
            $q = $this->quantity === "" ? 0 : (int)$this->quantity;
            $b = $this->broken_quantity === "" ? 0 : (int)$this->broken_quantity;
            
            $this->actual_quantity = max(0, $q - $b);
            $this->validateOnly($field);
        }

        if ($field === 'mode') {
            $this->resetValidation();
            if ($this->mode === 'existing') {
                $this->reset(['name', 'code', 'type', 'status', 'notes', 'specifications']);
            } else {
                $this->reset(['equipment_id']);
            }
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
        $rules = [
            'mode' => 'required|in:existing,new',
            'lab_id' => 'required|exists:labs,id',
            'quantity' => 'required|integer|min:1', // Yêu cầu ít nhất 1 thiết bị
            'broken_quantity' => 'required|integer|min:0|lte:quantity',
        ];

        if ($this->mode === 'existing') {
            $rules['equipment_id'] = 'required|exists:equipment,id';  
        } else {
            $rules['name'] = 'required|string|max:255';
            $rules['code'] = 'required|string|max:255|unique:equipment,code';  
            $rules['type'] = 'required|string|max:255';
            $rules['status'] = 'required|in:available,in_use,maintenance,broken';
            $rules['notes'] = 'nullable|string';
            $rules['specifications'] = 'nullable|array';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'mode.required' => 'Vui lòng chọn loại thêm thiết bị.',
            'lab_id.required' => 'Vui lòng chọn phòng Lab tiếp nhận.',
            'lab_id.exists' => 'Phòng Lab đã chọn không tồn tại trên hệ thống.',
            
            'equipment_id.required' => 'Vui lòng chọn một thiết bị từ danh sách.',
            'equipment_id.exists' => 'Thiết bị đã chọn không hợp lệ.',
            
            'name.required' => 'Tên thiết bị không được để trống.',
            'code.required' => 'Mã thiết bị không được để trống.',
            'code.unique' => 'Mã thiết bị này đã tồn tại trong hệ thống.',
            'type.required' => 'Vui lòng nhập loại thiết bị.',
            
            'quantity.required' => 'Số lượng tổng không được để trống.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập vào phải ít nhất là 1.',
            
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
            // 1. Xác định Equipment ID
            if ($this->mode === 'existing') {
                $equipmentId = (int) $this->equipment_id;
            } else {
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
            }

            // 2. Kiểm tra trùng lặp và cộng dồn
            $existingItem = LabEquipmentItem::where('lab_id', (int) $this->lab_id)
                ->where('equipment_id', (int) $equipmentId)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity'        => $existingItem->quantity + (int) $this->quantity,
                    'broken_quantity' => $existingItem->broken_quantity + (int) $this->broken_quantity,
                    'actual_quantity' => $existingItem->actual_quantity + (int) $this->actual_quantity,
                ]);
                $isMerged = true;
            } else {
                LabEquipmentItem::create([
                    'lab_id'          => (int) $this->lab_id,
                    'equipment_id'    => (int) $equipmentId,
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
                message: 'Đã thêm thiết bị vào phòng Lab thành công!'
            );
        }

        return redirect()->route('equipment.index');
    }

    public function render()
    {
        return view('livewire.admin.equipment.create', [
            'labs' => Lab::orderBy('name')->get(),
            'equipments' => Equipment::orderBy('name')->get(),
        ])->layout('components.layouts.admin-layout');
    }
}