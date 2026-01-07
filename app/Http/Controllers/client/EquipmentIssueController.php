<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\EquipmentIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role as RoleEnum;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\EquipmentIssueRequest;
use App\Models\EquipmentIssueRequestItem;
use Illuminate\Support\Facades\DB;

class EquipmentIssueController extends Controller
{
    /**
     * Hiển thị trang "Báo hỏng & lịch sử xử lý" cho 1 thiết bị.
     */
    public function index($equipmentId)
    {
        // Lấy danh sách báo hỏng theo thiết bị, mới nhất trước, phân trang
        $issues = EquipmentIssue::with(['reporter', 'logs.changer']) // Theo người báo cáo
            ->where('equipment_id', $equipmentId)
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('pages.client.equipment.issues.index', [
            'equipmentId' => $equipmentId,
            'issues'      => $issues,
        ]);
    }

    /**
     * Xử lý form gửi báo hỏng.
     */
    public function store(Request $request, $equipmentId)
    {
        // 1) Validate: chỉ cần mô tả + ảnh
        $data = $request->validate([
            'description' => 'required|string|min:3',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        // 2) Upload ảnh tạm vào storage/public 
        $tempPaths = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                // lưu tạm vào: storage/app/public/equipment_issue_requests/temp
                $tempPaths[] = $file->store('equipment_issue_requests/temp', 'public');
            }
        }

        $createdRequest = null;

        DB::transaction(function () use ($user, $equipmentId, $data, $tempPaths, &$createdRequest) {

            // 3) Tạo phiếu tổng (description = null)
            $createdRequest = EquipmentIssueRequest::create([
                'user_id'     => $user->id,
                'description' => null,
                'status'      => EquipmentIssueRequest::STATUS_PENDING,
                'items_count' => 1,
            ]);

            // 4) Move ảnh từ temp -> folder theo request_id
            $finalImages = [];

            foreach ($tempPaths as $tempPath) {
                $filename = basename($tempPath);
                $finalPath = "equipment_issue_requests/{$createdRequest->id}/{$filename}";

                Storage::disk('public')->move($tempPath, $finalPath);
                $finalImages[] = $finalPath; // lưu path 0 có "storage/" 
            }

            // 5) Tạo item chi tiết
            EquipmentIssueRequestItem::create([
                'request_id'   => $createdRequest->id,
                'equipment_id' => $equipmentId,
                'description'  => trim($data['description']),
                'images'       => $finalImages,
                'status'       => EquipmentIssueRequestItem::STATUS_PENDING,
            ]);
        });

        if (! $createdRequest) {
            return redirect()->back()->with('error', 'Không thể tạo phiếu báo hỏng.');
        }

        $this->notifyAdminsNewRequest($createdRequest, $data['description']);

        return redirect()
            ->back()
            ->with('success', 'Đã gửi phiếu báo hỏng. Vui lòng chờ admin xử lý.');
    }


    // Xóa báo hỏng
    public function destroy(EquipmentIssue $issue)
    {
        $user = Auth::user();

        $issueId = $issue->id;

        $issue->delete();

        return redirect()
            ->back()
            ->with('success', "Đã xóa báo hỏng #{$issueId}.");
    }



    private function notifyAdminsNewRequest(EquipmentIssueRequest $req, string $itemDescription): void
    {

        // Lấy danh sách admin 
        $admins = User::whereHas('role', function ($q) {
            $q->where('name', RoleEnum::Admin->value);
        })->get();

        if ($admins->isEmpty()) {
            return;
        }

        $sender = $req->user; // người tạo phiếu

        // title 
        $title = 'Đã tạo phiếu báo hỏng mới';

        // message = mô tả item 
        $desc = trim($itemDescription);
        $message = $desc !== '' ? $desc : ('Có ' . ($req->items_count ?? 1) . ' thiết bị được báo hỏng.');

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'equipment_issue_request',

                'title'   => $title,
                'message' => $message,

                'data'    => [
                    'request_id'  => $req->id,

                    'sender_id'   => $sender?->id,
                    'sender_name' => $sender?->full_name ?? $sender?->name ?? 'Người dùng',

                    'priority'    => null,

                    // link sang chi tiết phiếu ở admin
                    'url'         => route('admin.equipment-issue-requests.show', $req->id),
                ],
            ]);
        }
    }
}
