<?php

namespace Domain\PlayerFetch;

use Domain\PlayerFetch\Listeners\ServerStatusFetchedListener;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class PlayerFetchProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlayerFetchAdapterFactoryContract::class, function ($app) {
            return new PlayerFetchAdapterFactory();
        });

        Event::listen(
            ServerStatusFetched::class, [ServerStatusFetchedListener::class, 'handle']
        );
    }
}
