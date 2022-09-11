<?php

use App\Http\Controllers\Api\v1\MinecraftAggregateController;
use App\Http\Controllers\Api\v1\MinecraftBadgeController;
use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use App\Http\Controllers\Api\v1\PlayerWarningController;
use App\Http\Controllers\Api\v2\GameBanController;
use App\Http\Middleware\RequiresServerTokenScope;
use Domain\ServerTokens\ScopeKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('bans')->group(function () {
    Route::middleware(
        RequiresServerTokenScope::middleware(ScopeKey::BAN_UPDATE),
    )->group(function () {
        Route::post('ban', [GameBanController::class, 'ban']);
        Route::post('unban', [GameBanController::class, 'unban']);
        Route::post('convert_to_permanent', [GameBanController::class, 'convertToPermanent']);
    });

    Route::middleware([
        RequiresServerTokenScope::middleware(ScopeKey::BAN_LOOKUP),
    ])->group(function () {
        Route::post('status', [GameBanController::class, 'status']);
        Route::post('all', [GameBanController::class, 'all']);
    });
});

Route::prefix('warnings')->group(function () {
    Route::get('/', [PlayerWarningController::class, 'show'])
        ->middleware(RequiresServerTokenScope::middleware(ScopeKey::WARNING_LOOKUP));

    Route::post('/', [PlayerWarningController::class, 'store'])
        ->middleware(RequiresServerTokenScope::middleware(ScopeKey::WARNING_UPDATE));
});

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
    Route::get('badges', [MinecraftBadgeController::class, 'show']);
    Route::get('aggregate', [MinecraftAggregateController::class, 'show']);

    Route::middleware(
        RequiresServerTokenScope::middleware(ScopeKey::ACCOUNT_BALANCE_SHOW),
    )->group(function () {
        Route::get('balance', [MinecraftBalanceController::class, 'show']);
    });

    Route::middleware(
        RequiresServerTokenScope::middleware(ScopeKey::ACCOUNT_BALANCE_DEDUCT),
    )->group(function () {
        Route::post('balance/deduct', [MinecraftBalanceController::class, 'deduct']);
    });
});

Route::middleware(
    RequiresServerTokenScope::middleware(ScopeKey::TELEMETRY),
)->group(function () {
    Route::prefix('minecraft/telemetry')->group(function () {
        Route::post('seen', [MinecraftTelemetryController::class, 'playerSeen']);
    });
});
