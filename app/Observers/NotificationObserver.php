<?php

namespace App\Observers;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        broadcast(new NotificationCreated($notification));
    }
}
