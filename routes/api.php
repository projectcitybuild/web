<?php

use App\Http\Controllers\Api\v1\AccountSearchController;
use App\Http\Controllers\Api\v1\MinecraftPlayerAliasSearchController;
use App\Http\Controllers\Api\v1\MinecraftShowcaseWarpController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use App\Http\Controllers\Api\v1\StripeWebhookController;
use App\Http\Controllers\Api\v2\Minecraft\MinecraftPlayerController;
use App\Http\Controllers\Api\v2\Minecraft\MinecraftRegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->group(function () {
    Route::post('stripe', [StripeWebhookController::class, 'handleWebhook']);
});

Route::prefix('v1')
    ->name('v1.')
    ->group(function() {
        Route::get('accounts/search', AccountSearchController::class);
        Route::get('minecraft/aliases/search', MinecraftPlayerAliasSearchController::class);
    });

Route::prefix('v2')
    ->name('v2.')
    ->middleware('require-server-token')
    ->group(function() {
        Route::prefix('minecraft')->group(function () {
            Route::prefix('showcase')->group(function () {
                Route::get('/', [MinecraftShowcaseWarpController::class, 'index']);
                Route::post('/', [MinecraftShowcaseWarpController::class, 'store']);
                Route::get('{name}', [MinecraftShowcaseWarpController::class, 'show']);
                Route::post('{name}', [MinecraftShowcaseWarpController::class, 'update']);
            });

            Route::prefix('telemetry')->group(function () {
                Route::post('seen', [MinecraftTelemetryController::class, 'playerSeen']);
            });

            Route::prefix('player/{minecraft_uuid}')->group(function () {
                Route::get('/', MinecraftPlayerController::class);

                Route::prefix('register')->group(function () {
                    Route::post('/', [MinecraftRegisterController::class, 'store'])
                        ->middleware('throttle:3,1');

                    Route::put('/', [MinecraftRegisterController::class, 'update'])
                        ->middleware('throttle:12,1');
                });
            });
        });
    });
