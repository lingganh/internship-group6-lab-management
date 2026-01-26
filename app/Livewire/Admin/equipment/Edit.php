<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Lab;
use Illuminate\Support\Facades\DB;
use App\Models\EquipmentIssue;
use Livewire\WithPagination;
use App\Models\LabEquipmentItem;

class Edit extends Component
{
    use WithPagination;

    public $equipmentId;        // ID của bảng equipment
    public $labEquipmentItemId; // ID của dòng trong bảng lab_equipment_items
    public $lab_id ='1';
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

    protected $paginationTheme = 'bootstrap';

    public function mount($id)
    {
        // Lấy lab_id từ URL query string
        $this->lab_id = request()->query('lab_id');

        $eq = Equipment::with('labItems')->findOrFail($id);

        $this->equipmentId = $eq->id;
        $this->name = $eq->name;
        $this->code = $eq->code;
        $this->type = $eq->type;
        $this->status = $eq->status;
        $this->purchased_date = optional($eq->purchased_date)->format('Y-m-d');
        $this->notes = $eq->notes;
        $this->specifications = is_array($eq->specifications) ? $eq->specifications : (json_decode($eq->specifications, true) ?? []);

        // Tìm đúng dòng dữ liệu của thiết bị tại phòng lab này
        $labItem = LabEquipmentItem::where('equipment_id', $id)
            ->where('lab_id', $this->lab_id)
            ->first();

        if ($labItem) {
            $this->labEquipmentItemId = $labItem->id;
            $this->quantity = (int) $labItem->quantity;
            $this->broken_quantity = (int) $labItem->broken_quantity;
            $this->actual_quantity = (int) $labItem->actual_quantity;
        } else {
            // Nếu không tìm thấy, reset về 0 để tránh lỗi hiển thị
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
            'quantity' => 'required|integer|min:0',
            'broken_quantity' => 'required|integer|min:0|max:' . $this->quantity,
            'notes' => 'nullable|string',
        ];
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

    public function update()
    {
        $this->validate();

        $isMerged = false; // Biến kiểm tra xem có xảy ra cộng dồn hay không

        DB::transaction(function () use (&$isMerged) {
            // 1. Cập nhật bảng Equipment (Thông tin chung)
            $equipment = Equipment::findOrFail($this->equipmentId);
            $equipment->update([
                'name' => $this->name,
                'code' => $this->code,
                'type' => $this->type,
                'status' => $this->status,
                'notes' => $this->notes,
                'specifications' => json_encode($this->specifications),
            ]);

            $this->actual_quantity = max(0, (int) $this->quantity - (int) $this->broken_quantity);

            // 2. Kiểm tra xem phòng mới chọn đã có thiết bị này chưa (trừ dòng đang sửa)
            $existingItemInNewLab = LabEquipmentItem::where('lab_id', (int) $this->lab_id)
                ->where('equipment_id', (int) $this->equipmentId)
                ->where('id', '!=', $this->labEquipmentItemId)
                ->first();

            if ($existingItemInNewLab) {
                // TÌNH HUỐNG: Cộng dồn dữ liệu
                $existingItemInNewLab->update([
                    'quantity' => $existingItemInNewLab->quantity + (int) $this->quantity,
                    'broken_quantity' => $existingItemInNewLab->broken_quantity + (int) $this->broken_quantity,
                    'actual_quantity' => $existingItemInNewLab->actual_quantity + (int) $this->actual_quantity,
                ]);

                // Xóa bản ghi cũ vì đã gộp xong
                LabEquipmentItem::find($this->labEquipmentItemId)->delete();
                $isMerged = true;
            } else {
                // TÌNH HUỐNG: Cập nhật bình thường
                LabEquipmentItem::where('id', $this->labEquipmentItemId)->update([
                    'lab_id' => (int) $this->lab_id,
                    'quantity' => (int) $this->quantity,
                    'broken_quantity' => (int) $this->broken_quantity,
                    'actual_quantity' => (int) $this->actual_quantity,
                ]);
            }
        });

        // 3. Bắn thông báo dựa trên kết quả xử lý
        if ($isMerged) {
            $this->dispatch(
                'alert',
                type: 'info',
                message: 'Thiết bị đã tồn tại ở phòng này. Đã cộng dồn thành công!'
            );
        } else {
            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Cập nhật thiết bị thành công!'
            );
        }

        return redirect()->route('equipment.index');
    }

    public function render()
    {
        return view('livewire.admin.equipment.edit', [
            'labs' => Lab::orderBy('name')->get(),
            'issues' => EquipmentIssue::where('equipment_id', $this->equipmentId)->orderByDesc('created_at')->paginate(5, ['*'], 'issuesPage'),
            'labItems' => LabEquipmentItem::with('lab')->where('equipment_id', $this->equipmentId)->get(),
        ])->layout('components.layouts.admin-layout');
    }
}