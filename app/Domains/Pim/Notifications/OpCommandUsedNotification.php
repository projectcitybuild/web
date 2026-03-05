<?php

namespace App\Domains\Pim\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use App\Models\CommandAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OpCommandUsedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private readonly CommandAudit $audit,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['discord'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'discord' => 'discord-message',
        ];
    }

    public function toDiscord($notifiable)
    {
        return new DiscordMessage(
            embeds: [
                new DiscordEmbed(
                    title: 'Command Used',
                    author: new DiscordAuthor(name: $this->audit->actor),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Command',
                            value: $this->audit->command,
                        ),
                        new DiscordEmbedField(
                            name: 'IP',
                            value: $this->audit->ip,
                        ),
                        new DiscordEmbedField(
                            name: 'At',
                            value: $this->audit->created_at,
                        ),
                    ],
                ),
            ],
        );
    }
}
