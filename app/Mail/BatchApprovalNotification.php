<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BatchApprovalNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Collection $events;
    public string $roomCode;

    /**
     * @param Collection $events  Danh sách lịch đã duyệt
     * @param string $roomCode   Mã khóa mở cửa lab
     */
    public function __construct(Collection $events, string $roomCode)
    {
        $this->events = $events;
        $this->roomCode = $roomCode;
    }

    public function build()
    {
        return $this
            ->subject('Lịch phòng lab đã được phê duyệt')
            ->view('mail.batch-approval-notification');
    }
}
