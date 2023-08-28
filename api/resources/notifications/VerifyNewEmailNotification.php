<?php

namespace Resources\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class VerifyNewEmailAddressNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $confirmLink,
        private readonly int $expiryTimeInMins,
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
            ->subject('Verify new email address')
            ->greeting('Email Change Request')
            ->line('A request has been made to change your account\'s email address to this address. Use the below link if you wish to proceed.')
            ->action('Yes, use this email address', $this->confirmLink)
            ->line('If you have changed your mind, feel free to ignore this email.')
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
