<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Pesan baru dari {$this->message->sender->name}: {$this->message->subject}",
            'message_id' => $this->message->id,
            'sender_name' => $this->message->sender->name,
            'url' => route('messages.show', $this->message->id),
        ];
    }
}
