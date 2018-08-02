<?php

namespace Domains\Library\OAuth;

use Domains\Library\OAuth\Storage\OAuthSessionStorage;
use Domains\Library\OAuth\Storage\OAuthStorageContract;
use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
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
        $this->app->bind(OAuthStorageContract::class, 
                         OAuthSessionStorage::class);
    }
}
