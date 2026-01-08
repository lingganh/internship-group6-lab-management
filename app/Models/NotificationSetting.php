<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_enabled',
        'in_app_enabled',
        'booking_notifications',
        'event_notifications',
        'maintenance_notifications',
    ];

    protected $casts = [
        'email_enabled'            => 'boolean',
        'in_app_enabled'           => 'boolean',
        'booking_notifications'    => 'boolean',
        'event_notifications'      => 'boolean',
        'maintenance_notifications' => 'boolean',
    ];

    /*
     * Mỗi user có 1 bản ghi cấu hình thông báo 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
