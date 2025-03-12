<?php

namespace App\Core\Domains\Discord;

use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Notifications\Notification;

class DiscordNotificationChannel
{
    public function __construct(
        private readonly DiscordWebhook $discordWebhook,
    ) {}

    public function send($notifiable, Notification $notification)
    {
        if (! $url = $notifiable->routeNotificationFor('discord', $notification)) {
            return;
        }

        /** @var DiscordMessage $message */
        $payload = $notification->toDiscord($notifiable);

        $this->discordWebhook->send($url, $payload, wait: true);
    }
}
