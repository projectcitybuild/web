<?php

namespace App\Core\Domains\Discord;

use App\Models\DonationPerk;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

final class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend(
                'discord',
                fn ($app) => new DiscordNotificationChannel(new DiscordWebhook),
            );
        });
    }

    public function boot(): void
    {
        DonationPerk::observe($this->app->make(DiscordDonationPerkObserver::class));
    }
}
