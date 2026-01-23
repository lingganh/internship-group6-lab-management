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


        $this->notes = $eq->notes;


        $firstItem = $eq->labItems->first();

        if ($firstItem) {
            $this->lab_id = $firstItem->lab_id;
            $this->quantity = (int) $firstItem->quantity;
            $this->broken_quantity = (int) $firstItem->broken_quantity;
            $this->actual_quantity = (int) $firstItem->actual_quantity;
        } else {
            // Nếu thiết bị chưa được gán vào lab nào
            $this->lab_id = null;
            $this->quantity = 0;
            $this->broken_quantity = 0;
            $this->actual_quantity = 0;
        }
    }

    protected function messages()
    {
        return [
            'name.required' => 'Trường tên thiết bị không được bỏ trống.',
            'code.required' => 'Trường mã thiết bị không được bỏ trống.',
            'code.unique' => 'Mã thiết bị này đã tồn tại.',
            'type.required' => 'Trường loại thiết bị không được bỏ trống.',
            'lab_id.required' => 'Vui lòng chọn phòng Lab.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'broken_quantity.max' => 'Số lượng hỏng không được vượt quá tổng số lượng.',
        ];
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
                'purchased_date' => now(),
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
    public function updated($propertyName)
    {
        if ($propertyName === 'quantity' || $propertyName === 'broken_quantity') {

            $qty = $this->quantity === "" ? 0 : (int) $this->quantity;
            $broken = $this->broken_quantity === "" ? 0 : (int) $this->broken_quantity;

            $this->actual_quantity = max(0, $qty - $broken);

            $this->validateOnly($propertyName);
        }
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
            'labs' => Lab::orderBy('name')->get(),
            'issues' => $issues,
            'labItems' => $labItems,
        ])->layout('components.layouts.admin-layout');
    }
}
