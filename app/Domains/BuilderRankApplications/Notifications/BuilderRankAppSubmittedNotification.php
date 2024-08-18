<?php

namespace App\Domains\BuilderRankApplications\Notifications;

use App\Models\BuilderRankApplication;
use Awssat\Notifications\Messages\DiscordEmbed;
use Awssat\Notifications\Messages\DiscordMessage;
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
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'discordHook'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
            'discordHook' => 'discord-message',
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
        return (new MailMessage())
            ->subject('Builder rank application submitted')
            ->greeting('Hi '.$this->builderRankApplication->account->username.',')
            ->line('Your builder rank application has been submitted.')
            ->line('The Architect Council will review your submission as soon as possible.')
            ->action(
                text: 'Check Application Status',
                url: route('front.rank-up.status', $this->builderRankApplication->getKey()),
            )
            ->line('If you have any questions, please feel free to reach out to staff at any time.');
    }

    public function toDiscord()
    {
        return (new DiscordMessage)
            ->content('A new builder rank application has arrived.')
            ->embed(function (DiscordEmbed $embed) {
                $embed->title('Builder Rank Application', route('front.panel.builder-ranks.show', $this->builderRankApplication))
                    ->field('Current build rank', $this->builderRankApplication->current_builder_rank)
                    ->field('Build location', $this->builderRankApplication->build_location)
                    ->field('Build description', Str::limit($this->builderRankApplication->build_description, 500))
                    ->author($this->builderRankApplication->minecraft_alias);
            });
    }
}
