<?php

namespace App\Domains\Contact\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ContactNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ?string $name,
        private readonly string $email,
        private readonly string $subject,
        private readonly string $inquiry,
        private readonly string $ip,
        private readonly string $userAgent,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'discord'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
            'discord' => 'discord-message',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Contact Form Submitted')
            ->greeting($this->subject)
            ->from($this->email)
            ->line('Name: '.$this->name ?: 'Not given')
            ->line('IP: '.$this->ip)
            ->line('User Agent: '.$this->userAgent)
            ->line($this->inquiry);
    }

    public function toDiscord($notifiable)
    {
        return new DiscordMessage(
            content: 'A contact form inquiry has been submitted.',
            embeds: [
                new DiscordEmbed(
                    title: $this->subject,
                    description: Str::limit($this->inquiry, 2000),
                    author: new DiscordAuthor(
                        name: $this->email,
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Name',
                            value: $this->name ?: 'Not provided',
                        ),
                        new DiscordEmbedField(
                            name: 'IP',
                            value: $this->ip,
                        ),
                        new DiscordEmbedField(
                            name: 'User Agent',
                            value: $this->userAgent,
                        ),
                    ],
                ),
            ],
        );
    }
}
