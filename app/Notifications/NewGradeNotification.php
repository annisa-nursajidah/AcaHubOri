<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewGradeNotification extends Notification
{
    use Queueable;

    public function __construct(public Grade $grade)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Nilai baru untuk mata pelajaran {$this->grade->subject->nama}: {$this->grade->nilai}",
            'grade_id' => $this->grade->id,
            'subject' => $this->grade->subject->nama ?? '',
            'url' => route('grades.show', $this->grade->id),
        ];
    }
}
