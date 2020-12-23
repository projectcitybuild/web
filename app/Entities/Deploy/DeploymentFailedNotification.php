<?php


namespace App\Entities\Deploy;


use Awssat\Notifications\Channels\DiscordWebhookChannel;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DeploymentFailedNotification extends Notification
{
    private String $error;

    /**
     * DeploymentFailedNotification constructor.
     * @param String $error
     */
    public function __construct(string $error)
    {
        $this->error = $error;
    }

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
            ->error()
            ->from("Deployment")
            ->content("❌ Deployment Failed")
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title("Exception Details")
                    ->content($this->error);
            });
    }
}
