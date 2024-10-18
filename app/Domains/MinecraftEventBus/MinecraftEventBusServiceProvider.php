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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class MinecraftEventBusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(function (MinecraftPlayerUpdated $event) {
            Log::debug('Dispatching NotifyMinecraftPlayerUpdatedJob');

            $servers = $this->getServers();
            Log::debug('Fetched servers', $servers->toArray());

            $servers->each(
                fn ($server, $_) => NotifyMinecraftPlayerUpdatedJob::dispatch(
                    $server,
                    new MinecraftUUID($event->player->uuid),
                )
            );
        });

        Event::listen(function (MinecraftUuidBanned $event) {
            Log::debug('Dispatching NotifyUuidBannedJob');

            $this->getServers()->each(
                fn ($server, $_) => NotifyUuidBannedJob::dispatch($server, $event->ban),
            );
        });

        Event::listen(function (IpAddressBanned $event) {
            Log::debug('Dispatching NotifyIpBannedJob');

            $this->getServers()->each(
                fn ($server, $_) => NotifyIpBannedJob::dispatch($server, $event->ban),
            );
        });
    }

    private function getServers(): Collection
    {
        return Server::whereNotNull('web_port')->get();
    }
}
