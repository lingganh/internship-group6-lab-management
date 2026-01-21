<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) abort(403);

        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back();
    }

    public function open(Notification $notification)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        // chặn user mở notif của người khác
        abort_if($notification->user_id !== $user->id, 403);

        // mark as read
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        // redirect sang link 
        $url = data_get($notification->data, 'url');
        return $url ? redirect()->to($url) : redirect()->back();
    }
}
