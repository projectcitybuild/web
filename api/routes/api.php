<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;


Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);

    Route::post('/register', [RegistrationController::class, 'register']);

    Route::get('/email-verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->name('verification.verify')
        ->middleware('signed');

    Route::post('/email-verify/resend', [VerifyEmailController::class, 'resend'])

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/new-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    Route::post('/user/2fa/challenge', [TwoFactorAuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::prefix('/user/2fa')->group(function () {
        Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::get('/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show']);

        Route::post('/', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware('password.confirm');

        Route::delete('/', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware('password.confirm');

        Route::post('/qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->middleware('password.confirm');

        Route::get('/recovery-codes', [RecoveryCodeController::class, 'index'])
            ->middleware('password.confirm');

        Route::post('/recovery-codes', [RecoveryCodeController::class, 'store'])
            ->middleware('password.confirm');
    });

    Route::apiResource('/servers', ServerController::class);
    Route::apiResource('/groups', GroupController::class);
});
