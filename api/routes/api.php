<?php

use App\Http\Controllers\Account\BillingPortalController;
use App\Http\Controllers\Account\UpdateEmailController;
use App\Http\Controllers\Account\UpdatePasswordController;
use App\Http\Controllers\Account\UpdateUsernameController;
use App\Http\Controllers\AccountLink\MinecraftLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Donations\DonationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PlayerBanController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('/register', [RegistrationController::class, 'register'])
        ->middleware('throttle:6,1');

    Route::get('/account/email', [UpdateEmailController::class, 'update'])
        ->name("account.update-email.confirm");
});

$authMiddleware = ['auth:sanctum', 'verified'];

Route::middleware($authMiddleware)->group(function() {
    Route::get('/profile/me', [AccountController::class, 'me']);

    Route::put('/account/password', [UpdatePasswordController::class, 'update']);

    Route::post('/account/email', [UpdateEmailController::class, 'store'])
        ->middleware('throttle:2,1');

    Route::patch('/account/username', [UpdateUsernameController::class, 'update']);
    Route::patch('/account/link/minecraft', [MinecraftLinkController::class, 'store']);
    Route::get('/account/donations', [DonationController::class, 'index']);
    Route::post('/account/billing', [BillingPortalController::class, 'index']);

    Route::apiResource('/bans', PlayerBanController::class);
    Route::apiResource('/groups', GroupController::class);
    Route::apiResource('/servers', ServerController::class);
});
