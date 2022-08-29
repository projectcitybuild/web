<?php

use App\Http\Controllers\Api\v1\GameBanV1Controller;
use App\Http\Controllers\Api\v1\GroupApiController;
use App\Http\Controllers\Api\v1\MinecraftAuthTokenController;
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
    Route::post('list', [GameBanV1Controller::class, 'getBanList']);
    Route::post('store/ban', [GameBanV1Controller::class, 'storeBan']);
    Route::post('store/unban', [GameBanV1Controller::class, 'storeUnban']);
    Route::post('status', [GameBanV1Controller::class, 'getPlayerStatus']);
});

Route::prefix('auth')->group(function () {
    Route::post('minecraft', [MinecraftAuthTokenController::class, 'store']);
    Route::get('minecraft/{minecraftUUID}', [MinecraftAuthTokenController::class, 'show']);
});

Route::prefix('groups')->group(function () {
    Route::get('/', [GroupApiController::class, 'getAll']);
});
