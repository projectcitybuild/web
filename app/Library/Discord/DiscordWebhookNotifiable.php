<?php


namespace App\Library\Discord;


use Illuminate\Notifications\Notifiable;

class DiscordWebhookNotifiable
{
    use Notifiable;

    /**
     * Route notifications for the Discord channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForDiscord($notification)
    {
        return config('services.discord.webhook_url');
    }
}
