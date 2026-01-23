<?php

namespace App\Observers;

use App\Events\NotificationCreated;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        try {
            broadcast(new NotificationCreated($notification));
        } catch (\Throwable $e) {
            // Reverb down thì chỉ mất realtime, DB vẫn đã có notification
            Log::warning('Broadcast notification failed', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
