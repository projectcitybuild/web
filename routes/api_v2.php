<?php

use App\Domains\ServerTokens\ScopeKey;
use App\Http\Controllers\API\v1\MinecraftAggregateController;
use App\Http\Controllers\API\v1\MinecraftBadgeController;
use App\Http\Controllers\API\v1\MinecraftBalanceController;
use App\Http\Controllers\API\v1\MinecraftDonationTierController;
use App\Http\Controllers\API\v1\MinecraftShowcaseWarpController;
use App\Http\Controllers\API\v1\MinecraftTelemetryController;
use App\Http\Controllers\API\v1\PlayerWarningController;
use App\Http\Controllers\API\v2\GameIPBanController;
use App\Http\Controllers\API\v2\GamePlayerBanController;
use App\Http\Middleware\RequiresServerTokenScope;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')
    ->name('v2.')
    ->group(function() {
        Route::prefix('bans/player')->group(function () {
            Route::middleware(
                RequiresServerTokenScope::middleware(ScopeKey::BAN_UPDATE),
            )->group(function () {
                Route::post('ban', [GamePlayerBanController::class, 'ban']);
                Route::post('unban', [GamePlayerBanController::class, 'unban']);
                Route::post('convert_to_permanent', [GamePlayerBanController::class, 'convertToPermanent']);
            });

            Route::middleware([
                RequiresServerTokenScope::middleware(ScopeKey::BAN_LOOKUP),
            ])->group(function () {
                Route::post('status', [GamePlayerBanController::class, 'status']);
                Route::post('all', [GamePlayerBanController::class, 'all']);
            });
        });

        Route::prefix('bans/ip')->group(function () {
            Route::middleware(
                RequiresServerTokenScope::middleware(ScopeKey::BAN_UPDATE),
            )->group(function () {
                Route::post('ban', [GameIPBanController::class, 'ban']);
                Route::post('unban', [GameIPBanController::class, 'unban']);
            });

            Route::middleware([
                RequiresServerTokenScope::middleware(ScopeKey::BAN_LOOKUP),
            ])->group(function () {
                Route::get('status', [GameIPBanController::class, 'status']);
            });
        });

        Route::prefix('warnings')->group(function () {
            Route::get('/', [PlayerWarningController::class, 'show'])
                ->middleware(RequiresServerTokenScope::middleware(ScopeKey::WARNING_LOOKUP));

            Route::middleware([
                RequiresServerTokenScope::middleware(ScopeKey::WARNING_UPDATE),
            ])->group(function () {
                Route::post('/', [PlayerWarningController::class, 'store']);
                Route::post('acknowledge', [PlayerWarningController::class, 'acknowledge']);
            });
        });

        Route::prefix('minecraft')->group(function () {
            Route::prefix('{minecraftUUID}')->group(function () {
                Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
                Route::get('badges', [MinecraftBadgeController::class, 'show']);
                Route::get('aggregate', [MinecraftAggregateController::class, 'show']);

                Route::prefix('balance')->group(function () {
                    Route::get('/', [MinecraftBalanceController::class, 'show'])
                        ->middleware(RequiresServerTokenScope::middleware(ScopeKey::ACCOUNT_BALANCE_SHOW));

                    Route::post('deduct', [MinecraftBalanceController::class, 'deduct'])
                        ->middleware(RequiresServerTokenScope::middleware(ScopeKey::ACCOUNT_BALANCE_DEDUCT));
                });
            });

            Route::prefix('showcase-warps')->group(function () {
                Route::get('/', [MinecraftShowcaseWarpController::class, 'index'])
                    ->middleware(RequiresServerTokenScope::middleware(ScopeKey::SHOWCASE_WARPS_SHOW));

                Route::post('/', [MinecraftShowcaseWarpController::class, 'store'])
                    ->middleware(RequiresServerTokenScope::middleware(ScopeKey::SHOWCASE_WARPS_UPDATE));

                Route::get('{name}', [MinecraftShowcaseWarpController::class, 'show'])
                    ->middleware(RequiresServerTokenScope::middleware(ScopeKey::SHOWCASE_WARPS_SHOW));

                Route::post('{name}', [MinecraftShowcaseWarpController::class, 'update'])
                    ->middleware(RequiresServerTokenScope::middleware(ScopeKey::SHOWCASE_WARPS_UPDATE));
            });

            Route::post('telemetry/seen', [MinecraftTelemetryController::class, 'playerSeen'])
                ->middleware(RequiresServerTokenScope::middleware(ScopeKey::TELEMETRY));
        });
    });
