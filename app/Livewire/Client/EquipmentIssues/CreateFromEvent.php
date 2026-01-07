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

class CreateFromEvent extends Component
{
    use WithFileUploads;

    public ?int $labEventId = null;
    public ?LabEvent $event = null;
    public ?int $labId = null;

    public array $equipmentOptions = [];
    public ?int $selectedEquipmentId = null;

    public string $commonDescription = ''; // mô tả chung
    public string $description = '';       // mô tả từng thiết bị 
    public array $images = [];
    public array $items = [];

    public ?array $previewItem = null;

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
            'commonDescription'     => ['required', 'string', 'min:5'],

            'selectedEquipmentId'   => [
                'required',
                'integer',
                Rule::exists('equipment', 'id')->when($this->labId, function ($rule) {
                    return $rule->where('lab_id', $this->labId);
                }),
            ],

            'description'           => ['required', 'string', 'min:3'],
            'images'                => ['nullable', 'array', 'max:2'],
            'images.*'              => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }



    private function loadEvent(int $eventId): void
    {
        // reset toàn bộ state theo event cũ
        $this->resetValidation();
        $this->resetErrorBag();

        $this->items = [];
        $this->previewItem = null;

        $this->selectedEquipmentId = null;
        $this->description = '';
        $this->images = [];

        $this->commonDescription = '';

        $this->labEventId = $eventId;
        $this->event = \App\Models\LabEvent::with('lab')->findOrFail($eventId);

        $this->labId = $this->event->lab?->id;

        if (! $this->labId) {
            $this->equipmentOptions = [];
            $this->addError('selectedEquipmentId', 'Sự kiện này chưa gắn phòng/lab hợp lệ.');
            return;
        }

        $this->equipmentOptions = \App\Models\Equipment::query()
            ->where('lab_id', $this->labId)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn($e) => [
                'id'    => $e->id,
                'label' => $e->name . ' (' . $e->code . ')',
            ])
            ->all();
    }


    #[On('initIssueFromEvent')]
    public function initIssueFromEvent(int $eventId): void
    {
        $this->loadEvent($eventId);
    }

    public function updatedSelectedEquipmentId(): void
    {
        // Đổi thiết bị thì reset phần chi tiết + ảnh 
        $this->description = '';
        $this->images = [];
        $this->resetValidation(['selectedEquipmentId', 'description', 'images']);
    }

    private function resetFormState(): void
    {
        $this->reset([
            'labEventId',
            'event',
            'labId',
            'equipmentOptions',
            'selectedEquipmentId',
            'commonDescription',
            'description',
            'images',
            'items',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function addItem(): void
    {
        $this->validate();

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
            'images'          => $storedImages,
        ];

        // reset input item (không reset commonDescription)
        $this->selectedEquipmentId = null;
        $this->description = '';
        $this->images = [];

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
        $this->validateOnly('commonDescription');

        if (count($this->items) === 0) {
            $this->addError('items', 'Bạn phải thêm ít nhất 1 thiết bị.');
            return;
        }

        $user = Auth::user();
        $createdRequest = null;

        DB::transaction(function () use ($user, &$createdRequest) {
            $createdRequest = EquipmentIssueRequest::create([
                'user_id'      => $user->id,
                'lab_event_id' => $this->labEventId,
                'description'  => trim($this->commonDescription),
                'status'       => 'pending',
                'items_count'  => count($this->items),
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
                    'images'       => $newPaths,
                    'status'       => 'pending',
                ]);
            }

            $this->notifyAdminsNewRequest($createdRequest);
        });

        $this->reset(['commonDescription', 'selectedEquipmentId', 'description', 'images', 'items']);
        $this->resetValidation();

        if (! $createdRequest) {
            throw new \RuntimeException('Create request failed.');
        }

        $this->dispatch('issue-request-created', [
            'requestId' => $createdRequest->id,
            'message'   => 'Đã gửi phiếu báo hỏng. Vui lòng chờ admin xử lý.',
        ]);

        $this->dispatch('issueRequestCreated', requestId: $createdRequest->id);
    }


    private function notifyAdminsNewRequest(EquipmentIssueRequest $request): void
    {
        $user = Auth::user();

        $admins = User::whereHas('role', function ($q) {
            $q->where('name', RoleEnum::Admin->value);
        })->get();

        if ($admins->isEmpty()) return;

        $desc = trim($request->description ?? '');

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id, 
                'type'    => 'equipment_issue_request',
                'title'   => 'Đã tạo phiếu phản hồi sử dụng phòng mới!',
                'message' => $desc !== '' ? $desc : ('Có ' . $request->items_count . ' thiết bị được báo hỏng.'),
                'data'    => [
                    'request_id'  => $request->id,
                    'sender_id'   => $user->id,
                    'sender_name' => $user->full_name ?? $user->name ?? 'Người dùng',
                    'priority'    => null,
                    'url'         => route('admin.equipment-issue-requests.show', $request->id),
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
