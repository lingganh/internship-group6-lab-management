<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentIssueLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_issue_id',
        'changed_by',
        'from_status',
        'to_status',
        'notes',
    ];

    public function issue()
    {
        return $this->belongsTo(EquipmentIssue::class, 'equipment_issue_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
