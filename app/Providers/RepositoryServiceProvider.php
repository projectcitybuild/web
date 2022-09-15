<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Repositories\GameIPBans\GameIPBanEloquentRepository;
use Repositories\GameIPBans\GameIPBanRepository;
use Repositories\PlayerWarnings\PlayerWarningEloquentRepository;
use Repositories\PlayerWarnings\PlayerWarningRepository;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            abstract: GameIPBanRepository::class,
            concrete: GameIPBanEloquentRepository::class,
        );
        $this->app->bind(
            abstract: PlayerWarningRepository::class,
            concrete: PlayerWarningEloquentRepository::class,
        );
    }
}
