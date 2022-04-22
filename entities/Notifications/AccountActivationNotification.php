<?php

namespace Entities\Notifications;

use Entities\Models\Eloquent\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountActivationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $activationURL
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
            ->subject('Activate Your PCB Account')
            ->from('no-reply@projectcitybuild.com')

            ->greeting('Just One More Step')
            ->line('Click the button below to activate your Project City Build account.')
            ->action('Activate Account', $this->activationURL)
            ->line('Please note that this link will expire in 24 hours.')
            ->line('Didn\'t sign up? You can safely ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
