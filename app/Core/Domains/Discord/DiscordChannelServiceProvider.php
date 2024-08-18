<?php

namespace App\Core\Domains\Discord;

use Awssat\Notifications\Channels\DiscordWebhookChannel;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;

class DiscordChannelServiceProvider extends \Awssat\Notifications\DiscordChannelServiceProvider
{
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('discordHook', function ($app) {
                return new DiscordWebhookChannel($app->make(HttpClient::class));
            });
        });
    }
}
