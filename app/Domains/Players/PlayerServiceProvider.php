<?php

namespace App\Domains\Players;

use App\Domains\Players\Events\PlayerCreatedEvent;
use App\Domains\Players\Jobs\FetchPlayerAliasJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PlayerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(function (PlayerCreatedEvent $event) {
            $player = $event->player;

            if ($player->alias === null || $player->alias === '') {
                FetchPlayerAliasJob::dispatch($player);
            }
        });
    }
}
