<?php

use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v2\GameBanV2Controller;
use Illuminate\Support\Facades\Route;
use Library\APITokens\APITokenScope;
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

$isBanV2Enabled = ! Environment::isProduction();

if ($isBanV2Enabled) {
    Route::prefix('bans')->group(function () {
        Route::middleware([
            'auth:sanctum',
            'abilities:'.APITokenScope::BAN_UPDATE->value,
        ])->group(function () {
            Route::post('ban', [GameBanV2Controller::class, 'ban']);
            Route::post('unban', [GameBanV2Controller::class, 'unban']);
        });

        Route::middleware([
            'auth:sanctum',
            'abilities:'.APITokenScope::BAN_LOOKUP->value,
        ])->group(function () {
            Route::post('status', [GameBanV2Controller::class, 'status']);
        });
    });
}

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);

    Route::middleware([
        'auth:sanctum',
        'abilities:'.APITokenScope::ACCOUNT_BALANCE_SHOW->value,
    ])->group(function () {
        Route::get('balance', [MinecraftBalanceController::class, 'show']);
    });

    Route::middleware([
        'auth:sanctum',
        'abilities:'.APITokenScope::ACCOUNT_BALANCE_DEDUCT->value,
    ])->group(function () {
        Route::post('balance/deduct', [MinecraftBalanceController::class, 'deduct']);
    });
});
