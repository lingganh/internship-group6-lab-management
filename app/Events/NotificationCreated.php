<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravolt\Avatar\Facade as Avatar;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Notification $notification) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        $data = $this->notification->data ?? [];

        $senderName = $data['sender_name'] ?? 'Hệ thống';
        $senderId   = $data['sender_id'] ?? null;


        $senderAvatar = $senderName
            ? Avatar::create($senderName)->toBase64()
            : null;

        return [
            'id'           => $this->notification->id,
            'type'         => $this->notification->type,
            'title'        => $this->notification->title,
            'message'      => $this->notification->message,
            'url'          => $data['url'] ?? null,

            'sender_id'    => $senderId,
            'sender_name'  => $senderName,
            'sender_avatar' => $senderAvatar,

            'created_at'   => optional($this->notification->created_at)->toISOString(),
        ];
    }
}
