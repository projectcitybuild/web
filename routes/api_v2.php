<?php

use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftTelemetryController;
use App\Http\Controllers\Api\v2\GameBanV2Controller;
use Domain\ServerTokens\ScopeKey;
use Illuminate\Support\Facades\Route;
use Library\Environment\Environment;

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
        'server-token:'.ScopeKey::BAN_UPDATE->value,
    )->group(function () {
        Route::post('ban', [GameBanV2Controller::class, 'ban']);
        Route::post('unban', [GameBanV2Controller::class, 'unban']);
    });

    Route::middleware([
        'server-token:'.ScopeKey::BAN_LOOKUP->value,
    ])->group(function () {
        Route::post('status', [GameBanV2Controller::class, 'status']);
    });
});

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);

    Route::middleware(
        'server-token:'.ScopeKey::ACCOUNT_BALANCE_SHOW->value
    )->group(function () {
        Route::get('balance', [MinecraftBalanceController::class, 'show']);
    });

    Route::middleware(
        'server-token:'.ScopeKey::ACCOUNT_BALANCE_DEDUCT->value
    )->group(function () {
        Route::post('balance/deduct', [MinecraftBalanceController::class, 'deduct']);
    });
});

Route::middleware(
    'server-token:'.ScopeKey::TELEMETRY->value
)->group(function () {
    Route::prefix('minecraft/telemetry')->group(function () {
        Route::post('seen', [MinecraftTelemetryController::class, 'playerSeen']);
    });
});
