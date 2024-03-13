<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Bans\PlayerBanController;
use App\Http\Controllers\Manage\ManageBadgeController;
use App\Http\Controllers\Manage\ManageDonationController;
use App\Http\Controllers\Manage\ManageGroupController;
use App\Http\Controllers\Manage\ManageIPBanController;
use App\Http\Controllers\Manage\ManagePlayerBanController;
use App\Http\Controllers\Manage\ManagePlayerController;
use App\Http\Controllers\Manage\ManagePlayerWarningController;
use App\Http\Controllers\Manage\ManageServerController;
use App\Http\Controllers\Me\AccountBillingPortalController;
use App\Http\Controllers\Me\AccountController;
use App\Http\Controllers\Me\AccountDonationController;
use App\Http\Controllers\Me\UpdateEmailController;
use App\Http\Controllers\Me\UpdatePasswordController;
use App\Http\Controllers\Me\UpdateUsernameController;
use App\Http\Controllers\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Minecraft\MinecraftPlayerSyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('register', [RegistrationController::class, 'register'])
        ->middleware('throttle:6,1');

    Route::get('account/email', [UpdateEmailController::class, 'update'])
        ->name("account.update-email.confirm");

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('me', [AccountController::class, 'me']);
        Route::put('password', [UpdatePasswordController::class, 'update'])->middleware('throttle:2,1'); // 2 attempts per 1 min
        Route::post('email', [UpdateEmailController::class, 'store'])->middleware('throttle:2,1');
        Route::patch('username', [UpdateUsernameController::class, 'update']);
        Route::get('donations', [AccountDonationController::class, 'index']);
        Route::post('billing', AccountBillingPortalController::class);
    });
    Route::prefix('minecraft')->group(function () {
        Route::get('config', [MinecraftConfigController::class]);
        Route::get('player/{uuid}/sync', [MinecraftPlayerSyncController::class]);
    });
    Route::prefix('manage')->group(function () {
        Route::apiResource('bans/players', ManagePlayerBanController::class);
        Route::apiResource('bans/ips', ManageIPBanController::class);
        Route::apiResource('badges', ManageBadgeController::class);
        Route::apiResource('donations', ManageDonationController::class);
        Route::apiResource('groups', ManageGroupController::class);
        Route::apiResource('players', ManagePlayerController::class);
        Route::apiResource('servers', ManageServerController::class);
        Route::apiResource('warnings', ManagePlayerWarningController::class);
    });
});

// TODO: middleware for application keys
Route::prefix('bans')->group(function () {
    Route::prefix('uuid')->group(function () {
        Route::post('/', [PlayerBanController::class, 'store']);
        Route::delete('/', [PlayerBanController::class, 'delete']);
        Route::get('{minecraft_uuid}', [PlayerBanController::class, 'show']);
    });
});
