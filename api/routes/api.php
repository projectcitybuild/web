<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ResendVerificationEmailController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Auth\TwoFactorRecoveryController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Bans\PlayerBanController;
use App\Http\Controllers\Donate\DonateController;
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
use App\Http\Controllers\Me\TwoFactorSetupController;
use App\Http\Controllers\Me\UpdateEmailController;
use App\Http\Controllers\Me\UpdatePasswordController;
use App\Http\Controllers\Me\UpdateUsernameController;
use App\Http\Controllers\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Minecraft\MinecraftPlayerSyncController;
use Illuminate\Support\Facades\Route;

Route::post('login', [SessionController::class, 'store']);

Route::post('logout', [SessionController::class, 'destroy'])
    ->middleware('auth');

Route::post('register', RegistrationController::class)
    ->middleware('throttle:6,1');

Route::post('forgot-password', PasswordResetLinkController::class);

Route::post('reset-password', NewPasswordController::class);

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('verify-email/resend', ResendVerificationEmailController::class)
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('me/email', [UpdateEmailController::class, 'update'])
    ->name("account.update-email.confirm");

Route::prefix('2fa')->group(function () {
    Route::post('challenge', TwoFactorChallengeController::class)
        ->middleware('throttle:two-factor');
    Route::post('recovery', TwoFactorRecoveryController::class);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('me')->group(function () {
        Route::get('/', [AccountController::class, 'me']);
        Route::put('password', [UpdatePasswordController::class, 'update'])
            ->middleware('throttle:2,1'); // 2 attempts per 1 min
        Route::post('email', [UpdateEmailController::class, 'store'])
            ->middleware('throttle:2,1');
        Route::patch('username', [UpdateUsernameController::class, 'update']);
        Route::get('donations', [AccountDonationController::class, 'index']);
        Route::post('billing', AccountBillingPortalController::class);

        Route::prefix('2fa')->group(function () {
            Route::post('/', [TwoFactorSetupController::class, 'enable']);
            Route::delete('/', [TwoFactorSetupController::class, 'disable'])
                ->middleware('throttle:password-confirm');
            Route::get('recovery-codes', [TwoFactorSetupController::class, 'recoveryCodes']);
            Route::post('confirm', [TwoFactorSetupController::class, 'confirm']);
            Route::get('qr', [TwoFactorSetupController::class, 'qrCode'])
                ->middleware('throttle:2,1');
        });
    });
    Route::prefix('donate')->group(function () {
        Route::post('single', [DonateController::class, 'single']);
        Route::post('subscription', [DonateController::class, 'subscription']);
    });
    Route::prefix('minecraft')->group(function () {
        Route::get('config', MinecraftConfigController::class);
        Route::get('player/{uuid}/sync', MinecraftPlayerSyncController::class);
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
