<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
            abstract: PlayerWarningRepository::class,
            concrete: PlayerWarningEloquentRepository::class,
        );
    }
}
