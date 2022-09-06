<?php

use App\Http\Controllers\Api\v1\MinecraftAggregateController;
use App\Http\Controllers\Api\v1\MinecraftBadgeController;
use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\MinecraftShowcaseWarpController;
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
        Route::post('convert_to_permanent', [GameBanV2Controller::class, 'convertToPermanent']);
    });

    Route::middleware([
        RequiresServerTokenScope::middleware(ScopeKey::BAN_LOOKUP),
    ])->group(function () {
        Route::post('status', [GameBanV2Controller::class, 'status']);
        Route::post('all', [GameBanV2Controller::class, 'all']);
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
    });

    Route::post('telemetry/seen', [MinecraftTelemetryController::class, 'playerSeen'])
        ->middleware(RequiresServerTokenScope::middleware(ScopeKey::TELEMETRY));
});
