<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentIssue;
use Illuminate\Http\Request;
use App\Models\EquipmentIssueLog;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role as RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Models\EquipmentIssueRequestItem;
use App\Models\LabEquipmentItem;

class EquipmentIssueController extends Controller
{
    /**
     * Admin cập nhật trạng thái báo hỏng.
     */
    public function updateStatus(Request $request, EquipmentIssue $issue)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasRole(RoleEnum::Admin->value)) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái báo hỏng.');
        }

        $data = $request->validate([
            'status'           => 'required|in:pending,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        $oldStatus = $issue->status;
        $newStatus = $data['status'];

        $qtyUpdated = false;
        $qtyMessage = null;

        DB::transaction(function () use ($issue, $user, $data, $oldStatus, $newStatus, &$qtyUpdated, &$qtyMessage) {

            // 1) Cập nhật phiếu báo hỏng
            $issue->status           = $newStatus;
            $issue->resolution_notes = $data['resolution_notes'] ?? null;

            if (in_array($newStatus, ['resolved', 'closed'], true)) {
                $issue->resolved_at = $issue->resolved_at ?: now();
            } else {
                $issue->resolved_at = null;
            }

            $issue->save();

            // 2) Ghi lịch sử cập nhật
            EquipmentIssueLog::create([
                'equipment_issue_id' => $issue->id,
                'changed_by'         => $user->id,
                'from_status'        => $oldStatus,
                'to_status'          => $issue->status,
                'notes'              => $issue->resolution_notes,
            ]);

            /**
             * 3) Cập nhật số lượng theo phòng (lab_equipment_items)
             * - pending/in_progress -> resolved/closed: giảm broken_quantity
             * - resolved/closed -> pending/in_progress: tăng broken_quantity (mở lại)
             */
            $fromOpen   = in_array($oldStatus, ['pending', 'in_progress'], true);
            $toClosed   = in_array($newStatus, ['resolved', 'closed'], true);
            $fromClosed = in_array($oldStatus, ['resolved', 'closed'], true);
            $toOpen     = in_array($newStatus, ['pending', 'in_progress'], true);

            // chỉ xử lý khi có chuyển trạng thái "qua lại" giữa open <-> closed
            if (!(($fromOpen && $toClosed) || ($fromClosed && $toOpen))) {
                return;
            }

            $reqItem = EquipmentIssueRequestItem::with(['request.labEvent.lab'])
                ->where('equipment_issue_id', $issue->id)
                ->first();

            if (!$reqItem) {
                $qtyMessage = 'Không tìm thấy request-item liên kết để cập nhật số lượng.';
                return;
            }

            $qty = (int) ($reqItem->broken_quantity ?? 1);
            if ($qty < 1) $qty = 1;

            $labId = $reqItem->request?->labEvent?->lab?->id
                ?? $reqItem->request?->lab_id;


            if (!$labId) {
                $qtyMessage = 'Phiếu không gắn lab_event_id / lab_id nên không xác định được phòng để cập nhật số lượng.';
                return;
            }

            $labItem = LabEquipmentItem::where('lab_id', $labId)
                ->where('equipment_id', $issue->equipment_id)
                ->lockForUpdate()
                ->first();

            if (!$labItem) {
                $qtyMessage = 'Không tìm thấy thiết bị trong phòng tương ứng để cập nhật số lượng.';
                return;
            }

            $currentBroken = (int) $labItem->broken_quantity;
            $maxQty        = (int) $labItem->quantity;

            if ($fromOpen && $toClosed) {
                $labItem->broken_quantity = max(0, $currentBroken - $qty);
                $labItem->save();
                $qtyUpdated = true;
                return;
            }

            if ($fromClosed && $toOpen) {
                $labItem->broken_quantity = min($maxQty, $currentBroken + $qty);
                $labItem->save();
                $qtyUpdated = true;
                return;
            }
        });

        $msg = "Đã cập nhật trạng thái báo hỏng phiếu #{$issue->id}.";
        if (!$qtyUpdated && $qtyMessage) {
            $msg .= " (Lưu ý: {$qtyMessage})";
        }

        return redirect()->back()->with('success', $msg);
    }
}
