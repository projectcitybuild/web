<?php

use App\Domains\ServerTokens\ScopeKey;
use App\Http\Controllers\Api\v3\Minecraft\MinecraftRegisterController;
use App\Http\Middleware\RequiresServerTokenScope;
use Illuminate\Support\Facades\Route;

Route::prefix('v3')
    ->name('v3.')
    ->middleware('require-server-token')
    ->group(function() {
        Route::prefix('minecraft')->group(function () {
            Route::post('register', [MinecraftRegisterController::class, 'store'])
                ->middleware('throttle:3,1');

            Route::put('register', [MinecraftRegisterController::class, 'update'])
                ->middleware('throttle:12,1');
        });
    });
