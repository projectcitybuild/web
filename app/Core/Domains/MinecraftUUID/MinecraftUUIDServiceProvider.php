<?php

namespace App\Core\Domains\MinecraftUUID;

use App\Core\Data\MinecraftUUID;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class MinecraftUUIDServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Automatically construct a MinecraftUUID from an url parameter
        Route::bind('minecraft_uuid', function ($value) {
            return new MinecraftUUID($value);
        });
    }
}
