<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentIssueRequest;

class EquipmentIssueRequestController extends Controller
{
    /**
     * Danh sách phiếu báo hỏng thiết bị 
     */
    public function index()
    {
        return view('pages.admin.equipment_issue_requests.index');
    }

    /**
     * Trang chi tiết phiếu báo hỏng 
     */
    public function show(EquipmentIssueRequest $equipmentIssueRequest)
    {
        return view('pages.admin.equipment_issue_requests.show', [
            'issueRequest' => $equipmentIssueRequest,
        ]);
    }
}
