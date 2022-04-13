<?php

use App\Http\Controllers\Api\v1\MinecraftBalanceController;
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
    Route::post('stripe', 'StripeWebhookController@handleWebhook')->name('cashier.webhook');
});

Route::prefix('bans')->group(function () {
    Route::post('list', 'GameBanController@getBanList');
    Route::post('store/ban', 'GameBanController@storeBan');
    Route::post('store/unban', 'GameBanController@storeUnban');
    Route::post('status', 'GameBanController@getPlayerStatus');
});

Route::prefix('auth')->group(function () {
    Route::post('minecraft', 'MinecraftAuthTokenController@store');
    Route::get('minecraft/{minecraftUUID}', 'MinecraftAuthTokenController@show');
});

Route::prefix('minecraft/{minecraftUUID}')->group(function () {
    Route::get('donation-tiers', 'MinecraftDonationTierController@show');
    Route::get('balance', [MinecraftBalanceController::class, 'show']);
    Route::post('balance/deduct', [MinecraftBalanceController::class, 'deduct']);
});

Route::prefix('groups')->group(function () {
    Route::get('/', 'GroupApiController@getAll');
});
