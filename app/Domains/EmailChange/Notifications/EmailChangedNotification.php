<?php

namespace App\Domains\EmailChange\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EmailChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $newEmail,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your email address was changed')
            ->greeting('Email Address Updated')
            ->line('Your account\'s email address was changed to '.$this->newEmail.'.')
            ->line('If this was not you, please reach out to us immediately as your account may have been compromised.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
