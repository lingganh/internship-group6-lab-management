<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment;

class EquipmentIssueRequestItem extends Model
{
    use HasFactory;

    // Trạng thái từng mục trong phiếu
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'request_id',
        'equipment_id',
        'broken_quantity',
        'equipment_issue_id',
        'description',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function request()
    {
        return $this->belongsTo(EquipmentIssueRequest::class, 'request_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function equipmentIssue()
    {
        return $this->belongsTo(EquipmentIssue::class, 'equipment_issue_id');
    }
}
