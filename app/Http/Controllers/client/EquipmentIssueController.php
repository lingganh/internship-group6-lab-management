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
use Illuminate\Validation\ValidationException;
use App\Models\LabEquipmentItem;

class EquipmentIssueController extends Controller
{
    /**
     * Hiển thị trang "Báo hỏng & lịch sử xử lý" cho 1 thiết bị.
     */
    public function index($equipmentId)
    {
        // Lấy danh sách báo hỏng theo thiết bị, mới nhất trước, phân trang
        $issues = EquipmentIssue::with([
            'reporter',
            'logs.changer',
            'requestItem.request.lab:id,name,code',
            'requestItem.request.labEvent.lab:id,name,code',
        ])
            ->where('equipment_id', $equipmentId)
            ->orderByDesc('created_at')
            ->paginate(5);

        $labItems = LabEquipmentItem::with('lab:id,name,code')
            ->where('equipment_id', $equipmentId)
            ->get();


        return view('pages.client.equipment.issues.index', [
            'equipmentId' => $equipmentId,
            'issues'      => $issues,
            'labItems'    => $labItems,
        ]);
    }

    /**
     * Xử lý form gửi báo hỏng.
     */
    public function store(Request $request, $equipmentId)
    {
        // 1) Validate
        $data = $request->validate([
            'lab_id'          => 'required|exists:labs,id',
            'description' => 'required|string|min:3',
            'broken_quantity' => 'required|integer|min:1',
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

        try {
            DB::transaction(function () use ($user, $equipmentId, $data, $tempPaths, &$createdRequest) {

                $qtyBroken = (int) $data['broken_quantity'];
                if ($qtyBroken < 1) $qtyBroken = 1;

                $labItem = LabEquipmentItem::where('lab_id', (int)$data['lab_id'])
                    ->where('equipment_id', $equipmentId)
                    ->lockForUpdate()
                    ->first();

                if (! $labItem) {
                    throw ValidationException::withMessages([
                        'lab_id' => 'Thiết bị không thuộc phòng/lab đã chọn.',
                    ]);
                }

                $available = (int) $labItem->actual_quantity;
                if ($qtyBroken > $available) {
                    throw ValidationException::withMessages([
                        'broken_quantity' => "Số lượng hỏng ({$qtyBroken}) không được vượt quá số lượng thực ({$available}).",
                    ]);
                }

                // 3) Tạo phiếu tổng (description = null)
                $createdRequest = EquipmentIssueRequest::create([
                    'user_id'     => $user->id,
                    'lab_event_id' => null,
                    'lab_id'      => (int)$data['lab_id'],   // +
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
                    'broken_quantity' => $qtyBroken,
                    'description'  => trim($data['description']),
                    'images'       => $finalImages,
                    'status'       => EquipmentIssueRequestItem::STATUS_PENDING,
                ]);
            });
        } catch (ValidationException $e) {
            // nếu đã upload temp, nên dọn để khỏi rác
            foreach ($tempPaths as $tempPath) {
                if ($tempPath) Storage::disk('public')->delete($tempPath);
            }

            return back()->withErrors($e->errors())->withInput();
        }

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
            // if ($sender && $admin->id === $sender->id) {
            //     continue;
            // }
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
