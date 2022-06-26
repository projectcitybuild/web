<?php

namespace Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountPasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $passwordResetURL
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
            ->subject('Password Reset Confirmation')
            ->greeting('Password Recovery')
            ->line('You or somebody else has requested for your password to be reset. Use the below link if you wish to proceed.')
            ->action('Reset Your Password', $this->passwordResetURL)
            ->line('If you did not request this, you can safely ignore this email.')
            ->line('Please note that this link will expire in 20 minutes.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
