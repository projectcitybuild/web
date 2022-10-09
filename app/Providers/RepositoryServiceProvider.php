<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Repositories\GameIPBans\GameIPBanEloquentRepository;
use Repositories\GameIPBans\GameIPBanRepository;
use Repositories\PlayerWarnings\PlayerWarningEloquentRepository;
use Repositories\PlayerWarnings\PlayerWarningRepository;
use Repositories\ShowcaseApplications\ShowcaseApplicationEloquentRepository;
use Repositories\ShowcaseApplications\ShowcaseApplicationRepository;

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
        $this->app->bind(
            abstract: ShowcaseApplicationRepository::class,
            concrete: ShowcaseApplicationEloquentRepository::class,
        );
    }
}
