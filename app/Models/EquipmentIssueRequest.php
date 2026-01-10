<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentIssueRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lab_event_id',
        'lab_id',
        'description',
        'status',
        'items_count',
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EquipmentIssueRequestItem::class, 'request_id');
    }

    public function labEvent()
    {
        return $this->belongsTo(\App\Models\LabEvent::class, 'lab_event_id');
    }
    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }
}
