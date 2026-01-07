<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentIssue extends Model
{
    use HasFactory;
    // protected $table = 'equipment_issues';

    protected $fillable = [
        'equipment_id',
        'reported_by',
        'title',
        'description',
        'images',
        'status',
        'assigned_to',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'images'        => 'array',
        'resolved_at'   => 'datetime',
    ];

    /*
     * Thiết bị bị báo hỏng, chưa có bảng equipment
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /*
     * Người báo hỏng (user tạo ticket)
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /*
     * Người được phân công xử lý (kỹ thuật viên / admin)
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    //Lịch sử báo hỏng
    public function logs()
    {
        return $this->hasMany(EquipmentIssueLog::class, 'equipment_issue_id')
            ->orderByDesc('created_at');
    }
}
