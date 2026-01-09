<?php

namespace App\Livewire\Admin\EquipmentIssueRequests;

use App\Models\EquipmentIssueRequest;
use App\Models\EquipmentIssueRequestItem;
use Livewire\Component;
use App\Models\EquipmentIssue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LabEquipmentItem;


class Show extends Component
{
    public EquipmentIssueRequest $request;
    public ?EquipmentIssueRequestItem $selectedItem = null;

    public function mount(int $requestId)
    {
        $this->request = EquipmentIssueRequest::with([
            'user',
            'items.equipment',
            'labEvent.lab',
        ])->findOrFail($requestId);
    }

    public function showItem(int $itemId)
    {
        $this->selectedItem = $this->request->items->firstWhere('id', $itemId);
    }

    public function render()
    {
        // luôn refresh quan hệ items để thấy trạng thái mới
        $this->request->loadMissing([
            'user',
            'items.equipment',
            'labEvent.lab',
        ]);

        return view('livewire.admin.equipment-issue-requests.show', [
            'request' => $this->request,
            'items'   => $this->request->items,
        ]);
    }

    // Chấp nhận báo hỏng từ thiết bị
    public function approveItem(int $itemId)
    {
        $item = $this->request->items->firstWhere('id', $itemId);

        if (! $item) {
            session()->flash('error', 'Không tìm thấy mục báo hỏng.');
            return;
        }

        if ($item->status !== EquipmentIssueRequestItem::STATUS_PENDING) {
            session()->flash('error', 'Mục này đã được xử lý trước đó.');
            return;
        }

        $event = $this->request->labEvent()
            ->with('lab:id,code')
            ->first();

        $labId = $event?->lab?->id ?? $this->request->lab_id;


        if ($this->request->lab_event_id && ! $labId) {
            session()->flash('error', 'Phiếu có gắn sự kiện nhưng LabEvent không map được sang Lab (lab_code sai hoặc không tồn tại labs.code).');
            return;
        }

        $qtyBroken = (int) ($item->broken_quantity ?? 1);
        if ($qtyBroken < 1) $qtyBroken = 1;

        try {
            DB::transaction(function () use ($item, $labId, $qtyBroken) {

                $rawImages  = is_array($item->images) ? $item->images : [];
                $imagePaths = [];

                foreach ($rawImages as $path) {
                    if (! $path) continue;

                    if (str_starts_with($path, 'uploads/')) {
                        $imagePaths[] = $path;
                    } elseif (str_starts_with($path, 'equipment_issue_requests/')) {
                        $imagePaths[] = 'storage/' . $path;
                    } else {
                        $imagePaths[] = $path;
                    }
                }

                $labItemQuery = LabEquipmentItem::query()
                    ->where('equipment_id', $item->equipment_id);

                if ($labId) {
                    $labItem = (clone $labItemQuery)
                        ->where('lab_id', $labId)
                        ->lockForUpdate()
                        ->first();

                    if (! $labItem) {
                        throw new \RuntimeException('Không tìm thấy thiết bị này trong phòng/lab của phiếu để cập nhật số lượng.');
                    }
                } else {
                    $distinctLabs = (clone $labItemQuery)
                        ->distinct('lab_id')
                        ->count('lab_id');

                    if ($distinctLabs !== 1) {
                        throw new \RuntimeException('Không xác định được phòng/lab để cập nhật số lượng (thiết bị có thể thuộc nhiều lab).');
                    }

                    $labItem = (clone $labItemQuery)
                        ->lockForUpdate()
                        ->first();
                }

                if (! $labItem) {
                    throw new \RuntimeException('Không tìm thấy thiết bị này trong phòng để cập nhật số lượng.');
                }

                $available = max(0, (int)$labItem->quantity - (int)$labItem->broken_quantity);

                if ($qtyBroken > $available) {
                    throw new \RuntimeException("Số lượng hỏng ({$qtyBroken}) vượt quá số lượng thực còn lại ({$available}).");
                }

                $labItem->broken_quantity = (int) $labItem->broken_quantity + $qtyBroken;
                $labItem->save();

                $issue = EquipmentIssue::create([
                    'equipment_id'     => $item->equipment_id,
                    'broken_quantity'  => $qtyBroken,
                    'reported_by'      => $this->request->user_id,
                    'title'            => null,
                    'description'      => $item->description,
                    'images'           => $imagePaths,
                    'status'           => 'pending',
                    'priority'         => null,
                    'assigned_to'      => null,
                    'resolved_at'      => null,
                    'resolution_notes' => null,
                ]);

                $item->equipment_issue_id = $issue->id;
                $item->status = EquipmentIssueRequestItem::STATUS_APPROVED;
                $item->save();

                $this->refreshRequestStatus();
            });

            $this->request->refresh()->load(['user', 'items.equipment', 'labEvent.lab']);

            session()->flash('success', 'Đã chấp nhận báo hỏng và cập nhật số lượng thiết bị.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }



    // từ chối báo hỏng 
    public function rejectItem(int $itemId)
    {
        $item = $this->request->items->firstWhere('id', $itemId);

        if (! $item) {
            session()->flash('error', 'Không tìm thấy mục báo hỏng.');
            return;
        }

        // Chỉ cho phép từ chối khi còn pending
        if ($item->status !== EquipmentIssueRequestItem::STATUS_PENDING) {
            session()->flash('error', 'Mục này đã được xử lý trước đó.');
            return;
        }

        DB::transaction(function () use ($item) {
            // 1. Đánh dấu item bị từ chối
            $item->status = EquipmentIssueRequestItem::STATUS_REJECTED;
            $item->save();

            // 2. Cập nhật trạng thái phiếu tổng
            $this->refreshRequestStatus();
        });

        // Reload lại request để UI cập nhật
        $this->request->refresh()->load(['user', 'items.equipment']);

        session()->flash('success', 'Đã từ chối báo hỏng cho thiết bị.');
    }

    protected function refreshRequestStatus(): void
    {
        // Load lại items mới nhất
        $this->request->load('items');

        $items = $this->request->items;
        if ($items->isEmpty()) {
            $this->request->status = EquipmentIssueRequest::STATUS_COMPLETED;
            $this->request->save();
            return;
        }

        $hasPending  = $items->contains(fn($i) => $i->status === EquipmentIssueRequestItem::STATUS_PENDING);
        $hasApproved = $items->contains(fn($i) => $i->status === EquipmentIssueRequestItem::STATUS_APPROVED);

        if ($hasPending) {
            // Còn item pending -> phiếu đang xử lý
            if ($this->request->status === EquipmentIssueRequest::STATUS_PENDING) {
                $this->request->status = EquipmentIssueRequest::STATUS_IN_REVIEW;
            }
        } else {
            // Không còn pending
            if ($hasApproved) {
                // Có ít nhất 1 cái được chấp nhận -> coi như phiếu hoàn thành
                $this->request->status = EquipmentIssueRequest::STATUS_COMPLETED;
            } else {
                // Không pending, không approved ->  toàn bộ bị reject
                $this->request->status = EquipmentIssueRequest::STATUS_CANCELLED;
            }
        }

        $this->request->save();
    }

    // Hoàn tác phiếu
    public function undoApproveItem(int $itemId): void
    {
        $item = $this->request->items->firstWhere('id', $itemId);

        if (! $item) {
            session()->flash('error', 'Không tìm thấy mục báo hỏng.');
            return;
        }

        // Chỉ cho hoàn tác khi đã approved/accepted
        if ($item->status !== 'approved') {
            session()->flash('error', 'Chỉ hoàn tác khi mục đã được chấp nhận.');
            return;
        }

        DB::transaction(function () use ($item) {
            // 1) Xóa ticket đã tạo (nếu có)
            $ticketId = $item->equipment_issue_id ?? $item->ticket_id ?? null;

            if ($ticketId) {
                $ticket = \App\Models\EquipmentIssue::find($ticketId);
                if ($ticket) {
                    // nếu có SoftDeletes thì dùng $ticket->delete()
                    $ticket->delete();
                }
            }

            // 2) Reset item về pending + bỏ liên kết ticket
            $item->update([
                'status'             => 'pending',
                'equipment_issue_id' => null,
                'ticket_id'          => null,
            ]);

            // 3) Ghi log 
            if (method_exists($item, 'logs')) {
                $item->logs()->create([
                    'action'      => 'undo_approve',
                    'from_status' => 'approved',
                    'to_status'   => 'pending',
                    'changed_by' => Auth::id(),
                    'notes'       => 'Hoàn tác chấp nhận – reset ticket.',
                ]);
            }
        });

        // reload lại request + items để UI cập nhật
        $this->request->load('items');

        session()->flash('success', 'Đã hoàn tác. Mục báo hỏng trở về trạng thái chờ xử lý.');
    }
}
