<?php

use App\Http\Controllers\Api\v1\AccountSearchController;
use App\Http\Controllers\Api\v1\GroupApiController;
use App\Http\Controllers\Api\v1\MinecraftAuthTokenController;
use App\Http\Controllers\Api\v1\MinecraftPlayerAliasSearchController;
use App\Http\Controllers\Api\v1\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::name('v0.')->group(function() {
    Route::prefix('webhooks')->group(function () {
        Route::post('stripe', [StripeWebhookController::class, 'handleWebhook'])
            ->name('cashier.webhook');
    });

    Route::prefix('auth')->group(function () {
        Route::post('minecraft', [MinecraftAuthTokenController::class, 'store']);
        Route::get('minecraft/{minecraftUUID}', [MinecraftAuthTokenController::class, 'show']);
    });

    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupApiController::class, 'getAll']);
    });

    Route::get('accounts/search', AccountSearchController::class);
    Route::get('minecraft/aliases/search', MinecraftPlayerAliasSearchController::class);
});

Route::prefix('v1')->name('v1.')->group(function() {
    Route::prefix('webhooks')->group(function () {
        Route::post('stripe', [StripeWebhookController::class, 'handleWebhook'])
            ->name('cashier.webhook');
    });

    Route::prefix('auth')->group(function () {
        Route::post('minecraft', [MinecraftAuthTokenController::class, 'store']);
        Route::get('minecraft/{minecraftUUID}', [MinecraftAuthTokenController::class, 'show']);
    });

    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupApiController::class, 'getAll']);
    });

    Route::get('accounts/search', AccountSearchController::class);
    Route::get('minecraft/aliases/search', MinecraftPlayerAliasSearchController::class);
});
