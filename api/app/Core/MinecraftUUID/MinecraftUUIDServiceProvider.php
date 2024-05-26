<?php

namespace App\Core\MinecraftUUID;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MinecraftUUIDServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Automatically construct a MinecraftUUID from a url parameter
        // https://laravel.com/docs/11.x/routing#route-model-binding
        Route::bind('minecraft_uuid', function ($value) {
            return new MinecraftUUID($value);
        });
    }
}
