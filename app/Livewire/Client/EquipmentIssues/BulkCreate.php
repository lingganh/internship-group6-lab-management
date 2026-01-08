<?php

namespace App\Livewire\Client\EquipmentIssues;

use App\Models\EquipmentIssueRequest;
use App\Models\EquipmentIssueRequestItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role as RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class BulkCreate extends Component
{
    use WithFileUploads;

    public ?int $selectedEquipmentId = null;

    public string $description = '';

    public $images = [];

    public array $items = [];

    public array $viewingItem = [];

    protected $rules = [
        'selectedEquipmentId' => 'required|integer|exists:equipment,id',
        'description'         => 'required|string',
        'images.*'            => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
    ];

    protected $messages = [
        'selectedEquipmentId.required' => 'Vui lòng chọn thiết bị.',
        'selectedEquipmentId.exists'   => 'Thiết bị không hợp lệ.',
        'description.required'         => 'Mô tả không được bỏ trống.',
        'images.*.image'               => 'Mỗi file phải là ảnh.',
        'images.*.mimes'               => 'Chỉ chấp nhận ảnh jpg, jpeg, png, gif, webp.',
        'images.*.max'                 => 'Mỗi ảnh tối đa 2MB.',
    ];

    /**
     * Mỗi lần chọn lại ảnh, giới hạn tối đa 2 file.
     */
    public function updatedImages($value): void
    {
        // nếu user chọn > 2 ảnh thì cắt bớt + báo lỗi
        if (is_array($this->images) && count($this->images) > 2) {
            $this->images = array_slice($this->images, 0, 2);
            $this->addError('images', 'Chỉ được chọn tối đa 2 ảnh cho mỗi thiết bị.');
        } else {
            $this->resetErrorBag('images');
        }
    }

    public function addItem(): void
    {
        // validate lại toàn bộ trước khi thêm
        $this->validate();

        $equipment = Equipment::findOrFail($this->selectedEquipmentId);

        // Lưu file 
        $storedImages = [];

        //Không cho chọn thiết bị nếu đã có ở phiếu danh sách báo hỏng 
        if (collect($this->items)->contains(fn($i) => (int)$i['equipment_id'] === (int)$this->selectedEquipmentId)) {
            $this->addError('selectedEquipmentId', 'Thiết bị này đã có trong danh sách báo hỏng.');
            return;
        }

        if (is_array($this->images) && count($this->images)) {
            foreach ($this->images as $image) {
                $storedImages[] = $image->store(
                    'equipment_issue_requests/temp',
                    'public'
                );
            }
        }

        $desc = trim($this->description);

        $this->items[] = [
            'equipment_id'   => $equipment->id,
            'equipment_name' => $equipment->name,
            'description'    => $desc,
            'images'         => $storedImages,
        ];

        // reset form
        $this->reset(['selectedEquipmentId', 'description', 'images']);
        $this->resetValidation();
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        // xóa luôn file tạm đã lưu
        foreach ($this->items[$index]['images'] as $path) {
            Storage::disk('public')->delete($path);
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render()
    {
        // Ẩn thiết bị đã thêm khỏi dropdown
        $usedIds = collect($this->items)
            ->pluck('equipment_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $equipments = Equipment::query()
            ->when(count($usedIds), fn($q) => $q->whereNotIn('id', $usedIds))
            ->orderBy('name')
            ->get();


        return view('livewire.client.equipment-issues.bulk-create', [
            'equipments' => $equipments,
        ]);
    }

    // xem chi tiết 1 hàng 
    public function showItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            $this->viewingItem = [];
            return;
        }

        $this->viewingItem = $this->items[$index];
    }

    // Hàm lưu danh sách báo hỏng
    public function saveRequest(): void
    {
        if (! count($this->items)) {
            $this->addError('items', 'Vui lòng thêm ít nhất 1 thiết bị vào danh sách báo hỏng trước khi lưu.');
            return;
        }

        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $requestTitle = 'Phiếu báo hỏng thiết bị';
            $requestDescription = 'Phiếu báo hỏng gồm ' . count($this->items) . ' thiết bị.';

            // 1) Phiếu tổng
            $request = EquipmentIssueRequest::create([
                'user_id'     => $user->id,
                'title'       => $requestTitle,
                'description' => $requestDescription,
                'status'      => EquipmentIssueRequest::STATUS_PENDING,
                'items_count' => count($this->items),
            ]);

            // 2) Item chi tiết + move ảnh
            foreach ($this->items as $item) {
                $finalImagePaths = [];

                foreach ($item['images'] as $tempPath) {
                    if (! $tempPath) {
                        continue;
                    }

                    $filename = basename($tempPath);
                    $newPath  = 'equipment_issue_requests/' . $request->id . '/' . $filename;

                    Storage::disk('public')->move($tempPath, $newPath);
                    $finalImagePaths[] = $newPath;
                }

                EquipmentIssueRequestItem::create([
                    'request_id'   => $request->id,
                    'equipment_id' => $item['equipment_id'],
                    'description' => $item['description'],
                    'images'       => $finalImagePaths,
                    'status'       => EquipmentIssueRequestItem::STATUS_PENDING,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            $this->addError('items', 'Có lỗi khi lưu phiếu báo hỏng, vui lòng thử lại.');
            return;
        }

        // 3) Thông báo cho admin
        $admins = User::whereHas('role', function ($q) {
            $q->where('name', RoleEnum::Admin->value);
        })->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'equipment_issue_request', // thêm để phân loại

                // Dòng chứa link sang trang chi tiết danh sách phiếu báo hỏng
                'title'   => 'đã tạo phiếu báo hỏng mới',

                'message' => 'Có ' . $request->items_count . ' thiết bị được báo hỏng.',

                'data'    => [
                    'request_id'    => $request->id,

                    'sender_id'     => $user->id,
                    'sender_name'   => $user->full_name ?? $user->name ?? 'Người dùng',

                    'priority'      => null,

                    'url'         => route('admin.equipment-issue-requests.show', $request->id),
                ],
            ]);
        }

        // 4) Reset form + flash + refresh để Livewire re-render
        $this->reset(['selectedEquipmentId', 'description', 'images', 'items']);

        session()->flash('success', 'Đã gửi phiếu báo hỏng, admin sẽ xét duyệt báo hỏng của bạn!');

        $this->redirect(route('client.equipment.issues.bulk-create'), navigate: true);
    }
}
