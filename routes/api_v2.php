<?php

use App\Http\Controllers\Api\v1\MinecraftBadgeController;
use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use App\Http\Controllers\Api\v2\GameBanV2Controller;
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
        Route::post('ban', [GameBanV2Controller::class, 'ban']);
        Route::post('unban', [GameBanV2Controller::class, 'unban']);
    });

    Route::middleware([
        RequiresServerTokenScope::middleware(ScopeKey::BAN_LOOKUP),
    ])->group(function () {
        Route::post('status', [GameBanV2Controller::class, 'status']);
    });
});

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
    Route::get('badges', [MinecraftBadgeController::class, 'show']);

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
