<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Pengumuman baru: {$this->announcement->judul}",
            'announcement_id' => $this->announcement->id,
            'url' => route('announcements.show', $this->announcement->id),
        ];
    }
}
