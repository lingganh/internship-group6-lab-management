<?php

namespace App\Mail;

use App\Models\LabEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPendingEventNotification extends Mailable
{
    use Queueable, SerializesModels;

     public $event;
    public $senderName;
    public $action;

    
    public function __construct(LabEvent $event, $senderName, $action)
    {
        $this->event = $event;
        $this->senderName = $senderName;
        $this->action = $action;
    }

     
    public function build()
    {
         $subject = $this->action === 'created' 
            ? "🔔 Yêu cầu phê duyệt lịch mới - " . $this->event->lab_code
            : "🔄 Cập nhật lịch đặt phòng - " . $this->event->lab_code;

        return $this->subject($subject)
                    ->view('mail.admin-pending-event');  
    }
}