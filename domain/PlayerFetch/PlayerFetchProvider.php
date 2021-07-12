<?php

namespace Domain\PlayerFetch;

use Domain\PlayerFetch\Listeners\ServerStatusFetchedListener;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class PlayerFetchProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(
            ServerStatusFetched::class, [ServerStatusFetchedListener::class, 'handle']
        );
    }
}
