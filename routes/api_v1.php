<?php

use App\Http\Controllers\Api\v1\AccountSearchController;
use App\Http\Controllers\Api\v1\GroupAPIController;
use App\Http\Controllers\Api\v1\MinecraftAuthTokenController;
use App\Http\Controllers\Api\v1\MinecraftPlayerAliasSearchController;
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
    Route::post('stripe', [StripeWebhookController::class, 'handleWebhook'])
        ->name('cashier.webhook');
});

Route::prefix('auth')->group(function () {
    Route::post('minecraft', [MinecraftAuthTokenController::class, 'store']);
    Route::get('minecraft/{minecraftUUID}', [MinecraftAuthTokenController::class, 'show']);
});

Route::prefix('groups')->group(function () {
    Route::get('/', [GroupAPIController::class, 'getAll']);
});

Route::get('accounts/search', AccountSearchController::class);
Route::get('minecraft/aliases/search', MinecraftPlayerAliasSearchController::class);
