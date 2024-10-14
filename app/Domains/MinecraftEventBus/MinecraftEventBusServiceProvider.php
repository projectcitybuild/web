<?php

namespace App\Domains\MinecraftEventBus;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Domains\MinecraftEventBus\Jobs\NotifyIpBannedJob;
use App\Domains\MinecraftEventBus\Jobs\NotifyMinecraftPlayerUpdatedJob;
use App\Domains\MinecraftEventBus\Jobs\NotifyUuidBannedJob;
use App\Models\Server;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class MinecraftEventBusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(function (MinecraftPlayerUpdated $event) {
            Server::get()->each(
                fn ($server) => NotifyMinecraftPlayerUpdatedJob::dispatch(
                    $server,
                    new MinecraftUUID($event->player->uuid),
                ),
            );
        });

        Event::listen(function (MinecraftUuidBanned $event) {
            Server::get()->each(
                fn ($server) => NotifyUuidBannedJob::dispatch($server, $event->ban),
            );
        });

        Event::listen(function (IpAddressBanned $event) {
            Server::get()->each(
                fn ($server) => NotifyIpBannedJob::dispatch($server, $event->ban),
            );
        });
    }
}
