<?php

namespace App\Core\Domains\Discord;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend(
                'discord',
                fn ($app) => new DiscordNotificationChannel(new DiscordWebhook()),
            );
        });
    }
}
