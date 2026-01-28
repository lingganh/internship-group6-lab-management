<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\LabEvent;

class ApprovalNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $schedule;
    public $password;

    /**
     * Khởi tạo class. 
     * Laravel sẽ tự động "đóng gói" (serialize) $schedule và $password vào hàng đợi.
     */
    public function __construct(LabEvent $schedule, $password)
    {
        $this->schedule = $schedule;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo lịch phòng Lab: ' . $this->schedule->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.approval',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}