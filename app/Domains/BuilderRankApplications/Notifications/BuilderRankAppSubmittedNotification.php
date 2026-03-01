<?php

namespace App\Domains\BuilderRankApplications\Notifications;

use App\Core\Domains\Discord\Data\DiscordAuthor;
use App\Core\Domains\Discord\Data\DiscordEmbed;
use App\Core\Domains\Discord\Data\DiscordEmbedField;
use App\Core\Domains\Discord\Data\DiscordMessage;
use App\Core\Domains\Discord\Data\DiscordPoll;
use App\Core\Domains\Discord\Data\DiscordPollMedia;
use App\Models\BuilderRankApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

use function route;

class BuilderRankAppSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private BuilderRankApplication $builderRankApplication,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     *
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
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your builder rank application is in review')
            ->greeting('Hi '.$this->builderRankApplication->account->username.',')
            ->line('Your builder rank application has been submitted.')
            ->line('The Architect Council will review your submission as soon as possible.')
            ->action(
                text: 'Check Application Status',
                url: route('front.rank-up.status', $this->builderRankApplication->id),
            )
            ->line('If you have any questions, please feel free to reach out to architects or staff at any time.');
    }

    public function toDiscord($notifiable)
    {
        return new DiscordMessage(
            content: '<@&839756575153324032> A build rank application has arrived. Please vote on the poll below. Once a decision is reached, use the link to approve or deny the application.',
            threadName: $this->builderRankApplication->minecraft_alias,
            poll: new DiscordPoll(
                question: 'Promote?',
                answers: [
                    new DiscordPollMedia(
                        text: 'Yes',
                        emojiName: '✅',
                    ),
                    new DiscordPollMedia(
                        text: 'No',
                        emojiName: '❌',
                    ),
                ],
                durationInHours: 7 * 24,
            ),
            embeds: [
                new DiscordEmbed(
                    title: 'Builder Rank Application',
                    url: route('review.builder-ranks.show', $this->builderRankApplication),
                    author: new DiscordAuthor(
                        name: $this->builderRankApplication->minecraft_alias,
                    ),
                    fields: [
                        new DiscordEmbedField(
                            name: 'Current build rank',
                            value: $this->builderRankApplication->current_builder_rank,
                        ),
                        new DiscordEmbedField(
                            name: 'Build location',
                            value: $this->builderRankApplication->build_location,
                        ),
                        new DiscordEmbedField(
                            name: 'Build description',
                            value: Str::limit($this->builderRankApplication->build_description, 500),
                        ),
                    ],
                ),
            ],
        );
    }
}
