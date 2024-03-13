<?php

namespace App\Providers;

use App\Models\MinecraftUUID;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('minecraft_uuid', function ($value) {
            return new MinecraftUUID($value);
        });
    }
}
