<?php

namespace App\Domains\EmailChange\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class VerifyOldEmailAddressNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $confirmLink,
        private int $expiryTimeInMins,
    ) {
    }

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
            ->subject('Email Address Change was Requested')
            ->greeting('Email Change Request')
            ->line('You or somebody else has requested to change your account\'s email address. Use the below link if you wish to proceed.')
            ->action('Yes, proceed with the change', $this->confirmLink)
            ->line('If you did not request this, let us know immediately as your account has likely been compromised.')
            ->line('The above link will expire in '.$this->expiryTimeInMins.' minutes.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
