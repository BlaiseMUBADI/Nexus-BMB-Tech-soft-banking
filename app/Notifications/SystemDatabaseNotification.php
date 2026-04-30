<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemDatabaseNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly array $options = []
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->options['type'] ?? 'info',
            'icon' => $this->options['icon'] ?? 'fas fa-bell',
            'action_url' => $this->options['action_url'] ?? null,
            'action_label' => $this->options['action_label'] ?? null,
            'meta' => $this->options['meta'] ?? [],
        ];
    }
}
