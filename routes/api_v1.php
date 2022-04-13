<?php

use App\Http\Controllers\Api\v1\GameBanController;
use App\Http\Controllers\Api\v1\GroupApiController;
use App\Http\Controllers\Api\v1\MinecraftAuthTokenController;
use App\Http\Controllers\Api\v1\MinecraftBalanceController;
use App\Http\Controllers\Api\v1\MinecraftDonationTierController;
use App\Http\Controllers\Api\v1\StripeWebhookController;
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

Route::prefix('webhooks')->group(function () {
    Route::post('stripe', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook');
});

Route::prefix('bans')->group(function () {
    Route::post('list', [GameBanController::class, 'getBanList']);
    Route::post('store/ban', [GameBanController::class, 'storeBan']);
    Route::post('store/unban', [GameBanController::class, 'storeUnban']);
    Route::post('status', [GameBanController::class, 'getPlayerStatus']);
});

Route::prefix('auth')->group(function () {
    Route::post('minecraft', [MinecraftAuthTokenController::class, 'store']);
    Route::get('minecraft/{minecraftUUID}', [MinecraftAuthTokenController::class, 'show']);
});

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', [MinecraftDonationTierController::class, 'show']);
    Route::get('balance', [MinecraftBalanceController::class, 'show']);
    Route::post('balance/deduct', [MinecraftBalanceController::class, 'deduct']);
});

Route::prefix('groups')->group(function () {
    Route::get('/', [GroupApiController::class, 'getAll']);
});
