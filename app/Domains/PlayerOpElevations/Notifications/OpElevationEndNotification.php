<?php

namespace App\Domains\PlayerOpElevations\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use App\Models\PlayerOpElevation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OpElevationEndNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private readonly PlayerOpElevation $elevation,
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
                    title: 'Player cancelled OP elevation',
                    description: $this->elevation->reason,
                    author: DiscordAuthor::withMinecraftAvatar(
                        name: $this->elevation->player->alias,
                        uuid: $this->elevation->player->uuid,
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: 'UUID',
                            value: $this->elevation->player->uuid,
                        ),
                        new DiscordEmbedField(
                            name: 'At',
                            value: $this->elevation->ended_at,
                        ),
                    ],
                ),
            ],
        );
    }
}
