<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;


Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);

    Route::post('/register', [RegistrationController::class, 'register']);

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->name('verification.verify')
        ->middleware('signed');

    Route::post('/email/verify/resend', [VerifyEmailController::class, 'resend']);

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/new-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    Route::post('/user/2fa/challenge', [TwoFactorAuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    $fortifyAuthMiddleware = config(key: 'fortify.auth_middleware', default: 'auth').':'.config('fortify.guard');

    Route::get('/me', [AccountController::class, 'me']);

    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::put('/user/password', [PasswordController::class, 'update'])
        ->middleware($fortifyAuthMiddleware);

    Route::get('/user/confirm-password/status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware($fortifyAuthMiddleware);

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware($fortifyAuthMiddleware);


    $twoFactorMiddleware = [$fortifyAuthMiddleware, 'password.confirm'];

    Route::prefix('/user/2fa')->middleware($twoFactorMiddleware)->group(function () {
        Route::post('/', [TwoFactorAuthenticationController::class, 'store']);
        Route::delete('/', [TwoFactorAuthenticationController::class, 'destroy']);
        Route::post('/qr-code', [TwoFactorQrCodeController::class, 'show']);
        Route::get('/secret-key', [TwoFactorSecretKeyController::class, 'show']);
        Route::get('/recovery-codes', [RecoveryCodeController::class, 'index']);
        Route::post('/recovery-codes', [RecoveryCodeController::class, 'store']);
    });

    Route::apiResource('/servers', ServerController::class);
    Route::apiResource('/groups', GroupController::class);
});
