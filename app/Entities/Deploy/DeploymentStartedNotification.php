<?php


namespace App\Entities\Deploy;


use Awssat\Notifications\Channels\DiscordWebhookChannel;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DeploymentStartedNotification extends Notification
{
    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [DiscordWebhookChannel::class];
    }

    public function toDiscord() : SlackMessage
    {
        return (new SlackMessage())
            ->info()
            ->from("Deployment")
            ->content("🕒 Deployment has begun...");
    }
}
