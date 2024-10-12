<?php

namespace App\Domains\MinecraftRegistration\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MinecraftRegistrationCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $name,
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
            ->subject('Your Project City Build account has been created')
            ->greeting('Welcome to Project City Build')
            ->line('__You now have full access to our Minecraft server__')
            ->line('Your in-game rank will automatically update. In the rare case where nothing happens, please use the `/sync` command or reconnect to the server.')
            ->line('---')
            ->line('## What\'s next?')
            ->line('If you wish to finish setting-up your Project City Build account, you will need to set a password.')
            ->action('Reset your password', route('front.password-reset.create'))
            ->line('This step is optional, but we highly recommend it as it grants you access to your player stats, settings and more.')
            ->line('---')
            ->line('## Discord')
            ->line('Announcements, events and discussions happen [on our Discord]('.config('discord.invite_url').') - be sure to join!')
            ->line('---')
            ->line('Thank you for joining our community!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
