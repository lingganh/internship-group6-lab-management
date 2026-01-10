<?php

namespace App\Livewire\Client\EquipmentIssues;

use App\Models\Equipment;
use App\Models\EquipmentIssueRequest;
use App\Models\LabEvent;
use App\Models\Notification;
use App\Models\User;
use App\Enums\Role as RoleEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LabEquipmentItem;

class CreateFromEvent extends Component
{
    use WithFileUploads;

    public ?int $labEventId = null;
    public ?LabEvent $event = null;
    public ?int $labId = null;

    public array $equipmentOptions = [];
    public ?int $selectedEquipmentId = null;

    public string $description = '';       // mô tả từng thiết bị 
    public array $images = [];
    public array $items = [];

    public string $feedback = '';
    public bool $alreadySubmitted = false;

    public ?array $previewItem = null;

    public int $brokenQuantity = 1;
    public ?int $selectedActualQuantity = null;
    public ?int $selectedTotalQuantity = null;
    public ?int $selectedBrokenQuantityCurrent = null;

    protected $listeners = [
        'submitIssueRequest' => 'saveRequest',
    ];

    public function mount(?int $labEventId = null): void
    {
        if ($labEventId) {
            $this->loadEvent($labEventId);
        }
    }

    protected function rules(): array
    {
        return [
            'feedback'     => ['required', 'string', 'min:5'],

            'selectedEquipmentId' => [
                'required',
                'integer',
                $this->labId
                    ? Rule::exists('lab_equipment_items', 'equipment_id')
                    ->where(fn($q) => $q->where('lab_id', $this->labId))
                    : Rule::exists('equipment', 'id'),
            ],

            'description'           => ['required', 'string', 'min:3'],
            'images'                => ['nullable', 'array', 'max:2'],
            'images.*'              => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }



    private function loadEvent(int $eventId): void
    {
        // reset state
        $this->resetValidation();
        $this->resetErrorBag();

        $this->items = [];
        $this->previewItem = null;

        $this->selectedEquipmentId = null;
        $this->description = '';
        $this->images = [];

        $this->feedback = '';
        $this->alreadySubmitted = false;

        $this->labEventId = $eventId;

        // load event + lab
        $this->event = \App\Models\LabEvent::with('lab')->findOrFail($eventId);
        $this->labId = $this->event->lab?->id;

        // feedback + lock
        $this->feedback = (string) ($this->event->feedback ?? '');
        $this->alreadySubmitted = filled($this->event->feedback);

        //  Nếu đã gửi -> load lại phiếu + items để hiển thị, rồi khóa form
        if ($this->alreadySubmitted) {
            $req = EquipmentIssueRequest::with(['items.equipment:id,name,code'])
                ->where('lab_event_id', $eventId)
                ->latest('id')
                ->first();

            if ($req) {

                $this->items = $req->items->map(function ($it) {
                    $label = $it->equipment
                        ? ($it->equipment->name . ' (' . $it->equipment->code . ')')
                        : ('#' . $it->equipment_id);

                    return [
                        'equipment_id'    => (int) $it->equipment_id,
                        'equipment_label' => $label,
                        'broken_quantity'  => (int) ($it->broken_quantity ?? 1),
                        'description'     => (string) ($it->description ?? ''),
                        'images'          => (array) ($it->images ?? []),
                    ];
                })->values()->all();
            }

            $this->equipmentOptions = []; // khóa dropdown
            return;
        }

        // chưa gửi mà event không có lab
        if (! $this->labId) {
            $this->equipmentOptions = [];
            $this->addError('selectedEquipmentId', 'Sự kiện này chưa gắn phòng/lab hợp lệ.');
            return;
        }

        // load thiết bị theo lab
        $this->equipmentOptions = LabEquipmentItem::query()
            ->with('equipment:id,name,code')
            ->where('lab_id', $this->labId)
            ->where('actual_quantity', '>', 0)
            ->get()
            ->filter(fn($item) => $item->equipment)
            ->sortBy(fn($item) => $item->equipment->name)
            ->map(fn($item) => [
                'id'    => $item->equipment_id,
                'label' => $item->equipment->name . ' (' . $item->equipment->code . ')',
            ])
            ->values()
            ->all();
    }



    #[On('initIssueFromEvent')]
    public function initIssueFromEvent(int $eventId): void
    {
        $this->loadEvent($eventId);
    }

    public function updatedSelectedEquipmentId(): void
    {
        $this->description = '';
        $this->images = [];
        $this->brokenQuantity = 1;

        $this->selectedActualQuantity = null;
        $this->selectedTotalQuantity = null;
        $this->selectedBrokenQuantityCurrent = null;

        if ($this->labId && $this->selectedEquipmentId) {
            $labItem = LabEquipmentItem::where('lab_id', $this->labId)
                ->where('equipment_id', $this->selectedEquipmentId)
                ->first();

            if ($labItem) {
                $this->selectedActualQuantity = (int) $labItem->actual_quantity;
                $this->selectedTotalQuantity = (int) $labItem->quantity;
                $this->selectedBrokenQuantityCurrent = (int) $labItem->broken_quantity;
            }
        }

        $this->resetValidation(['selectedEquipmentId', 'description', 'images', 'brokenQuantity']);
    }

    private function resetFormState(): void
    {
        $this->reset([
            'labEventId',
            'event',
            'labId',
            'equipmentOptions',
            'selectedEquipmentId',
            'feedback',
            'description',
            'images',
            'items',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function addItem(): void
    {
        $this->validate([
            'selectedEquipmentId' => $this->rules()['selectedEquipmentId'],
            'description'         => ['required', 'string', 'min:3'],
            'brokenQuantity'      => ['required', 'integer', 'min:1'],
            'images'              => ['nullable', 'array', 'max:2'],
            'images.*'            => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $labItem = LabEquipmentItem::where('lab_id', $this->labId)
            ->where('equipment_id', $this->selectedEquipmentId)
            ->first();

        if (! $labItem) {
            $this->addError('selectedEquipmentId', 'Thiết bị này không thuộc phòng/lab của sự kiện.');
            return;
        }

        $available = (int) $labItem->actual_quantity;
        if ($this->brokenQuantity > $available) {
            $this->addError('brokenQuantity', "Số lượng hỏng không được vượt quá số lượng thực ({$available}).");
            return;
        }

        // chống trùng thiết bị
        if (collect($this->items)->contains(fn($i) => (int)$i['equipment_id'] === (int)$this->selectedEquipmentId)) {
            $this->addError('selectedEquipmentId', 'Thiết bị này đã có trong danh sách.');
            return;
        }

        $equipment = \App\Models\Equipment::findOrFail($this->selectedEquipmentId);

        // lưu file ảnh tạm 
        $storedImages = [];
        foreach ($this->images ?? [] as $img) {
            $storedImages[] = $img->store('equipment_issue_requests/temp', 'public');
        }

        $this->items[] = [
            'equipment_id'    => (int) $equipment->id,
            'equipment_label' => $equipment->name . ' (' . $equipment->code . ')',
            'description'     => trim($this->description),
            'broken_quantity' => (int) $this->brokenQuantity,
            'images'          => $storedImages,
        ];

        $this->selectedEquipmentId = null;
        $this->description = '';
        $this->images = [];
        $this->brokenQuantity = 1;
        $this->selectedActualQuantity = null;
        $this->selectedTotalQuantity = null;
        $this->selectedBrokenQuantityCurrent = null;

        $this->resetValidation(['selectedEquipmentId', 'description', 'images', 'images.*']);
    }


    public function showItem(int $index): void
    {
        $this->previewItem = $this->items[$index] ?? null;

        if (! $this->previewItem) {
            return;
        }

        // mở preview modal 
        $this->dispatch('open-modal', id: 'issuePreviewModal');
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function saveRequest(): void
    {
        if ($this->alreadySubmitted) {
            $this->dispatch('toaster', 'Bạn đã gửi phản hồi rồi.');
            return;
        }
        if ($this->event && filled($this->event->feedback)) {
            $this->dispatch('toaster', 'Bạn đã gửi phản hồi cho lịch này rồi.');
            return;
        }

        if (EquipmentIssueRequest::where('lab_event_id', $this->labEventId)->exists()) {
            $this->dispatch('toaster', 'Lịch này đã có phiếu phản hồi rồi.');
            return;
        }

        $this->validate([
            'feedback' => ['required', 'string', 'min:5'],
        ]);


        // if (count($this->items) === 0) {
        //     $this->addError('items', 'Bạn phải thêm ít nhất 1 thiết bị.');
        //     return;
        // }

        $user = Auth::user();
        $createdRequest = null;
        $hasItems = count($this->items) > 0;


        $this->event->update([
            'feedback' => trim($this->feedback),
        ]);

        DB::transaction(function () use ($user, &$createdRequest, $hasItems) {

            $createdRequest = EquipmentIssueRequest::create([
                'user_id'      => $user->id,
                'lab_event_id' => $this->labEventId,
                'description'  => trim($this->feedback),
                'status' => $hasItems
                    ? EquipmentIssueRequest::STATUS_PENDING
                    : EquipmentIssueRequest::STATUS_COMPLETED,
                'items_count'  => count($this->items),
            ]);

            LabEvent::whereKey($this->labEventId)->update([
                'feedback' => trim($this->feedback),
            ]);

            foreach ($this->items as $item) {
                $newPaths = [];

                foreach (($item['images'] ?? []) as $tempPath) {
                    if (!Storage::disk('public')->exists($tempPath)) continue;

                    $filename = basename($tempPath);
                    $newPath  = "equipment_issue_requests/{$createdRequest->id}/{$filename}";
                    Storage::disk('public')->move($tempPath, $newPath);
                    $newPaths[] = $newPath;
                }

                $createdRequest->items()->create([
                    'equipment_id' => $item['equipment_id'],
                    'description'  => $item['description'],
                    'broken_quantity'  => (int)($item['broken_quantity'] ?? 1),
                    'images'       => $newPaths,
                    'status'       => 'pending',
                ]);
            }

            $this->notifyAdminsNewRequest($createdRequest);
        });

        $this->reset(['selectedEquipmentId', 'description', 'images', 'items']);
        $this->alreadySubmitted = true;

        $this->resetValidation();

        if (! $createdRequest) {
            throw new \RuntimeException('Create request failed.');
        }

        $this->dispatch('issue-request-created', [
            'requestId' => $createdRequest->id,
            'message' => $hasItems
                ? 'Đã gửi phiếu báo hỏng. Vui lòng chờ admin xử lý.'
                : 'Đã gửi phản hồi. Cảm ơn bạn.',

        ]);

        $this->dispatch('issueRequestCreated', requestId: $createdRequest->id);
    }


    private function notifyAdminsNewRequest(EquipmentIssueRequest $request): void
    {
        $user = Auth::user(); // SENDER: người thực hiện hành động (người tạo phiếu)

        // RECEIVERS: danh sách admin sẽ nhận thông báo
        $admins = User::whereHas('role', function ($q) {
            $q->where('name', RoleEnum::Admin->value);
        })->get();

        if ($admins->isEmpty()) return;

        // Nội dung hiển thị ngắn trong list thông báo (nên fallback nếu rỗng)
        $desc = trim((string) ($request->description ?? ''));

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id, // RECEIVER: user_id của người nhận thông báo

                // UI dùng để hiển thị: title + message (mô tả)
                'title'   => 'Đã tạo phiếu phản hồi sử dụng phòng mới!',
                'message' => $desc !== '' ? $desc : 'Có phiếu mới cần xử lý.',

                // data: payload để UI biết ai gửi + click đi đâu + liên kết tới đối tượng nghiệp vụ
                'data' => [
                    'request_id'  => $request->id,
                    // OBJECT REF: ID của đối tượng nghiệp vụ liên quan đến thông báo (ở đây là EquipmentIssueRequest).
                    // Khi tái sử dụng cho chức năng khác, hãy đổi key + giá trị theo đối tượng tương ứng:
                    //   - event_id  => $event->id (tạo/duyệt sự kiện)
                    //   - booking_id => $booking->id (đặt lịch)
                    //   - issue_id  => $issue->id (báo hỏng 1 thiết bị)
                    // Có thể bỏ hẳn nếu bạn không cần truy vết theo object và đã có 'url' để điều hướng.

                    'sender_id'   => $user->id,    // SENDER id
                    'sender_name' => $user->full_name ?? $user->name ?? 'Người dùng', // SENDER display
                    'url'         => route('admin.equipment-issue-requests.show', $request->id), // Click chuyển trang
                ],
            ]);
        }
    }


    public function getSelectableEquipmentOptionsProperty(): array
    {
        $picked = collect($this->items)
            ->pluck('equipment_id')
            ->map(fn($id) => (int) $id)
            ->all();

        return array_values(array_filter($this->equipmentOptions, function ($opt) use ($picked) {
            return !in_array((int) ($opt['id'] ?? 0), $picked, true);
        }));
    }

    public function render()
    {
        return view('livewire.client.equipment-issues.create-from-event');
    }
}
