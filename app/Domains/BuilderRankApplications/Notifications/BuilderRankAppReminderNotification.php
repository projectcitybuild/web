<?php

namespace App\Domains\BuilderRankApplications\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

use function route;

class BuilderRankAppReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public readonly Collection $openApps,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
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
            content: '<@&839756575153324032> The following applications are still open. Please review them as soon as possible.',
            embeds: $this->openApps->map(function ($app) {
                return new DiscordEmbed(
                    author: new DiscordAuthor(name: $app->minecraft_alias),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Submitted',
                            value: $app->created_at->diffForHumans(),
                        ),
                        new DiscordEmbedField(
                            name: 'URL',
                            value: route('review.builder-ranks.show', $app),
                        ),
                    ],
                );
            })->toArray(),
        );
    }
}
