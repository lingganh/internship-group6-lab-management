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

        DB::transaction(function () use ($issue, $user, $data, $oldStatus) {

            // 1) Cập nhật phiếu báo hỏng
            $issue->status           = $data['status'];
            $issue->resolution_notes = $data['resolution_notes'] ?? null;

            if (in_array($data['status'], ['resolved', 'closed'], true)) {
                if (!$issue->resolved_at) {
                    $issue->resolved_at = now();
                }
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

            // 3) Cập nhật trạng thái thiết bị
            $equipment = $issue->equipment;
            if (!$equipment) {
                return;
            }

            // Nếu issue này đang ở trạng thái "mở" -> chắc chắn thiết bị đang hỏng
            if (in_array($issue->status, ['pending', 'in_progress'], true)) {
                $equipment->status = 'broken';
                $equipment->save();
                return;
            }

            // Nếu issue này chuyển sang resolved/closed:
            // kiểm tra xem còn issue nào đang mở cho thiết bị này không
            $hasOpenIssues = EquipmentIssue::where('equipment_id', $equipment->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->exists();

            if ($hasOpenIssues) {
                // vẫn còn issue mở -> giữ trạng thái hỏng
                $equipment->status = 'broken';
            } else {
                // không còn issue mở -> cho thiết bị về trạng thái bình thường
                // ĐỔI 'available' cho đúng với giá trị enum trong migration equipment
                $equipment->status = 'available';
            }

            $equipment->save();
        });

        return redirect()
            ->back()
            ->with('success', "Đã cập nhật trạng thái báo hỏng phiếu #{$issue->id}.");
    }
}
