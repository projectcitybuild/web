<?php

namespace Entities\Notifications;

use Awssat\Notifications\Messages\DiscordEmbed;
use Awssat\Notifications\Messages\DiscordMessage;
use Entities\Models\Eloquent\BuilderRankApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BuilderRankCouncilNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private BuilderRankApplication $builderRankApplication)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['discordHook'];
    }

    public function toDiscord(): DiscordMessage
    {
        return (new DiscordMessage)
            ->content('A new builder rank application has arrived.')
            ->embed(function (DiscordEmbed $embed) {
                $embed->title('Builder Rank Application', route('front.panel.builder-ranks.show', $this->builderRankApplication))
                    ->field('Current build rank', $this->builderRankApplication->current_builder_rank)
                    ->field('Build location', $this->builderRankApplication->build_location)
                    ->field('Build description', $this->builderRankApplication->build_description)
                    ->author($this->builderRankApplication->minecraft_alias);
            });
    }
}
