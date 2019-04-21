<?php

namespace App\Library\OAuth;

use App\Library\OAuth\Storage\OAuthSessionStorage;
use App\Library\OAuth\Storage\OAuthStorageContract;
use Illuminate\Support\ServiceProvider;
use App\Library\OAuth\Storage\OAuthMemoryStorage;

final class OAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningUnitTests())
        {
            $this->app->bind(OAuthStorageContract::class, OAuthMemoryStorage::class);
        }
        else
        {
            $this->app->bind(OAuthStorageContract::class, OAuthSessionStorage::class);
        }
    }
}
