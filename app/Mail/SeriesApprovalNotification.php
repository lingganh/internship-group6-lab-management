<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SeriesApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Collection $events,
        public string $roomCode
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $firstEvent = $this->events->first();
        $count = $this->events->count();
        
        return new Envelope(
            subject: "Lịch lặp đã được phê duyệt ({$count} buổi) - {$firstEvent->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.series-approval',
            with: [
                'events' => $this->events,
                'roomCode' => $this->roomCode,
                'firstEvent' => $this->events->first(),
                'eventCount' => $this->events->count(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}