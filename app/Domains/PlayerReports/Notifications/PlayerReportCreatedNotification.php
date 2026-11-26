<?php

namespace App\Domains\PlayerReports\Notifications;

use App\Core\Data\JobQueue;
use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use App\Models\PlayerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class PlayerReportCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private PlayerReport $playerReport,
    ) {}

    public function via(object $notifiable): array
    {
        return ['discord'];
    }

    public function viaQueues(): array
    {
        return [
            'discord' => JobQueue::DISCORD->value,
        ];
    }

    public function toDiscord(object $notifiable): DiscordMessage
    {
        return new DiscordMessage(
            content: 'A new player report has been submitted.',
            threadName: Str::limit(
                "{$this->playerReport->reported_player_name} - Report #{$this->playerReport->id}",
                100,
            ),
            embeds: [
                new DiscordEmbed(
                    title: "Player Report #{$this->playerReport->id}",
                    description: Str::limit($this->playerReport->details, 500),
                    url: route('review.player-reports.show', $this->playerReport),
                    author: new DiscordAuthor(
                        name: $this->playerReport->reporter_name,
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Reported Player',
                            value: $this->playerReport->reported_player_name,
                        ),
                        new DiscordEmbedField(
                            name: 'Subject',
                            value: $this->playerReport->subject,
                        ),
                    ],
                ),
            ],
        );
    }
}
