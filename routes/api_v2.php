<?php

use App\Http\Controllers\Api\v1\MinecraftAggregateController;
use App\Http\Controllers\Api\v1\MinecraftBadgeController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftShowcaseWarpController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')
    ->name('v2.')
    ->middleware('require-server-token')
    ->group(function() {
        Route::prefix('minecraft')->group(function () {
            Route::prefix('{minecraftUUID}')->group(function () {
                Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
                Route::get('badges', [MinecraftBadgeController::class, 'show']);
                Route::get('aggregate', [MinecraftAggregateController::class, 'show']);
            });

            Route::prefix('showcase-warps')->group(function () {
                Route::get('/', [MinecraftShowcaseWarpController::class, 'index']);
                Route::post('/', [MinecraftShowcaseWarpController::class, 'store']);
                Route::get('{name}', [MinecraftShowcaseWarpController::class, 'show']);
                Route::post('{name}', [MinecraftShowcaseWarpController::class, 'update']);
            });

            Route::post('telemetry/seen', [MinecraftTelemetryController::class, 'playerSeen']);
        });
    });
