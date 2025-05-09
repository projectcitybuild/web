<?php

namespace App\Domains\MinecraftRegistration\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MinecraftRegistrationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $code
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Activate Your PCB Account')
            ->greeting('Just One More Step')
            ->line('Use the below code to activate your account.')
            ->line('# '.$this->code)
            ->line('The above code will expire in 1 hour.')
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
