<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Đánh dấu tất cả thông báo của admin hiện tại là đã đọc.
     */
    public function markAllRead()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // Cập nhật tất cả notifications chưa đọc
        $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back();
    }
}
