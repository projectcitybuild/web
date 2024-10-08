<?php

use App\Http\Controllers\Api\v1\MinecraftAggregateController;
use App\Http\Controllers\Api\v1\MinecraftBadgeController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftShowcaseWarpController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use App\Http\Controllers\Api\v1\PlayerWarningController;
use App\Http\Controllers\Api\v2\GameIPBanController;
use App\Http\Controllers\Api\v2\GamePlayerBanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')
    ->name('v2.')
    ->group(function() {
        Route::prefix('bans/player')->group(function () {
            Route::middleware('require-server-token')->group(function () {
                Route::post('ban', [GamePlayerBanController::class, 'ban']);
                Route::post('unban', [GamePlayerBanController::class, 'unban']);
                Route::post('convert_to_permanent', [GamePlayerBanController::class, 'convertToPermanent']);
            });

            Route::middleware('require-server-token')->group(function () {
                Route::post('status', [GamePlayerBanController::class, 'status']);
                Route::post('all', [GamePlayerBanController::class, 'all']);
            });
        });

        Route::prefix('bans/ip')->group(function () {
            Route::middleware('require-server-token')->group(function () {
                Route::post('ban', [GameIPBanController::class, 'ban']);
                Route::post('unban', [GameIPBanController::class, 'unban']);
            });

            Route::middleware('require-server-token')->group(function () {
                Route::get('status', [GameIPBanController::class, 'status']);
            });
        });

        Route::prefix('warnings')->group(function () {
            Route::get('/', [PlayerWarningController::class, 'show'])
                ->middleware('require-server-token');

            Route::middleware('require-server-token')->group(function () {
                Route::post('/', [PlayerWarningController::class, 'store']);
                Route::post('acknowledge', [PlayerWarningController::class, 'acknowledge']);
            });
        });

        Route::prefix('minecraft')->group(function () {
            Route::prefix('{minecraftUUID}')->group(function () {
                Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
                Route::get('badges', [MinecraftBadgeController::class, 'show']);
                Route::get('aggregate', [MinecraftAggregateController::class, 'show']);
            });

            Route::prefix('showcase-warps')->group(function () {
                Route::get('/', [MinecraftShowcaseWarpController::class, 'index'])
                    ->middleware('require-server-token');

                Route::post('/', [MinecraftShowcaseWarpController::class, 'store'])
                    ->middleware('require-server-token');

                Route::get('{name}', [MinecraftShowcaseWarpController::class, 'show'])
                    ->middleware('require-server-token');

                Route::post('{name}', [MinecraftShowcaseWarpController::class, 'update'])
                    ->middleware('require-server-token');
            });

            Route::post('telemetry/seen', [MinecraftTelemetryController::class, 'playerSeen'])
                ->middleware('require-server-token');
        });
    });
