<?php

namespace App\Core\Domains\Discord;

use App\Core\Domains\Discord\Data\DiscordMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class DiscordNotificationChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (! $url = $notifiable->routeNotificationFor('discord', $notification)) {
            return;
        }

        /** @var DiscordMessage $message */
        $message = $notification->toDiscord($notifiable);

        return Http::asJson()->post($url, $message->toJson());
    }
}
