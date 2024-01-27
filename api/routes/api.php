<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Manage\BadgeController;
use App\Http\Controllers\Manage\DonationController;
use App\Http\Controllers\Manage\GroupController;
use App\Http\Controllers\Manage\PlayerBanController;
use App\Http\Controllers\Manage\PlayerController;
use App\Http\Controllers\Manage\PlayerWarningController;
use App\Http\Controllers\Manage\ServerController;
use App\Http\Controllers\Me\AccountBillingPortalController;
use App\Http\Controllers\Me\AccountController;
use App\Http\Controllers\Me\AccountDonationController;
use App\Http\Controllers\Me\UpdateEmailController;
use App\Http\Controllers\Me\UpdatePasswordController;
use App\Http\Controllers\Me\UpdateUsernameController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('register', [RegistrationController::class, 'register'])
        ->middleware('throttle:6,1');

    Route::get('account/email', [UpdateEmailController::class, 'update'])
        ->name("account.update-email.confirm");
});

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::prefix('account')->group(function () {
        Route::get('me', [AccountController::class, 'me']);
        Route::put('password', [UpdatePasswordController::class, 'update'])->middleware('throttle:2,1');
        Route::post('email', [UpdateEmailController::class, 'store'])->middleware('throttle:2,1');
        Route::patch('username', [UpdateUsernameController::class, 'update']);
        Route::get('donations', [AccountDonationController::class, 'index']);
        Route::post('billing', AccountBillingPortalController::class);
    });
    Route::prefix('manage')->group(function () {
        Route::apiResource('bans', PlayerBanController::class);
        Route::apiResource('badges', BadgeController::class);
        Route::apiResource('donations', DonationController::class);
        Route::apiResource('groups', GroupController::class);
        Route::apiResource('players', PlayerController::class);
        Route::apiResource('servers', ServerController::class);
        Route::apiResource('warnings', PlayerWarningController::class);
    });
});
