<?php

use App\Http\Controllers\Front\Account\AccountBillingController;
use App\Http\Controllers\Front\Account\AccountDonationController;
use App\Http\Controllers\Front\Account\AccountGameAccountController;
use App\Http\Controllers\Front\Account\AccountInfractionsController;
use App\Http\Controllers\Front\Account\AccountProfileController;
use App\Http\Controllers\Front\Account\AccountSecurityController;
use App\Http\Controllers\Front\Account\AccountSettingController;
use App\Http\Controllers\Front\Account\Mfa\DisableMfaController;
use App\Http\Controllers\Front\Account\Mfa\FinishMfaController;
use App\Http\Controllers\Front\Account\Mfa\ResetBackupController;
use App\Http\Controllers\Front\Account\Mfa\SetupMfaController;
use App\Http\Controllers\Front\Account\Mfa\StartMfaController;
use App\Http\Controllers\Front\BanAppeal\BanAppealController;
use App\Http\Controllers\Front\BanAppeal\BanLookupController;
use App\Http\Controllers\Front\BanlistController;
use App\Http\Controllers\Front\BuilderRankApplicationController;
use App\Http\Controllers\Front\DonationController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LoginController;
use App\Http\Controllers\Front\LogoutController;
use App\Http\Controllers\Front\MfaBackupController;
use App\Http\Controllers\Front\MfaLoginGateController;
use App\Http\Controllers\Front\MinecraftPlayerLinkController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\PasswordResetController;
use App\Http\Controllers\Front\ReauthController;
use App\Http\Controllers\Front\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('logout', [LogoutController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('p/{name}', [PageController::class, 'index'])
    ->name('page');

Route::prefix('donate')->group(function () {
    Route::get('/', [DonationController::class, 'index'])
        ->name('donate');

    Route::post('checkout', [DonationController::class, 'checkout'])
        ->name('donations.checkout');

    Route::get('success', [DonationController::class, 'success'])
        ->name('donate.success');
});

Route::prefix('rank-up')->group(function () {
    Route::get('/', [BuilderRankApplicationController::class, 'index'])
        ->name('rank-up');

    Route::post('/', [BuilderRankApplicationController::class, 'store'])
        ->name('rank-up.submit');

    Route::get('{id}', [BuilderRankApplicationController::class, 'show'])
        ->name('rank-up.status');
});

Route::prefix('appeal')->group(function () {
    Route::get('/', [BanAppealController::class, 'index'])
        ->name('appeal');

    Route::redirect('auth', '/appeal')
        ->name('appeal.auth')
        ->middleware('auth');

    Route::get('{banAppeal}', [BanAppealController::class, 'show'])
        ->name('appeal.show');
});

Route::prefix('bans')->group(function () {
    Route::get('/', [BanlistController::class, 'index'])
        ->name('banlist');

    Route::post('/', BanLookupController::class)
        ->name('bans.lookup');

    Route::get('{ban}/appeal', [BanAppealController::class, 'create'])
        ->name('appeal.create');

    Route::post('{ban}/appeal', [BanAppealController::class, 'store'])
        ->name('appeal.submit');
});

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'show'])
        ->name('login')
        ->middleware('guest');

    Route::post('/', [LoginController::class, 'login'])
        ->name('login.submit')
        ->middleware('guest');

    Route::get('reactivate', [LoginController::class, 'resendActivationEmail'])
        ->name('login.reactivate')
        ->middleware(['guest', 'throttle:3,1']);

    Route::get('reauth', [ReauthController::class, 'show'])
        ->name('password.confirm')
        ->middleware('auth');

    Route::post('reauth', [ReauthController::class, 'process'])
        ->name('password.confirm.submit')
        ->middleware(['auth', 'throttle:6,1']);

    Route::get('mfa', [MfaLoginGateController::class, 'create'])
        ->name('login.mfa')
        ->middleware(['auth', 'active-mfa']);

    Route::post('mfa', [MfaLoginGateController::class, 'store'])
        ->name('login.mfa.submit')
        ->middleware(['auth', 'active-mfa', 'throttle:6,1']);

    Route::get('mfa/recover', [MfaBackupController::class, 'show'])
        ->name('login.mfa-recover')
        ->middleware(['auth', 'active-mfa']);

    Route::delete('mfa/recover', [MfaBackupController::class, 'destroy'])
        ->name('login.mfa-recover.submit')
        ->middleware(['auth', 'active-mfa', 'throttle:6,1']);
});

Route::prefix('password-reset')->group(function () {
    Route::get('/', [PasswordResetController::class, 'create'])
        ->name('password-reset.create');

    Route::post('/', [PasswordResetController::class, 'store'])
        ->name('password-reset.store');

    Route::get('edit', [PasswordResetController::class, 'edit'])
        ->name('password-reset.edit')
        ->middleware('signed');

    Route::patch('edit', [PasswordResetController::class, 'update'])
        ->name('password-reset.update');
});

Route::prefix('register')->group(function () {
    Route::get('/', [RegisterController::class, 'show'])
        ->name('register')
        ->middleware('guest');

    Route::post('/', [RegisterController::class, 'register'])
        ->name('register.submit')
        ->middleware('guest');

    Route::get('activate', [RegisterController::class, 'activate'])
        ->name('register.activate')
        ->middleware(['signed', 'guest']);
});

Route::group([
    'prefix' => 'account',
    'middleware' => 'auth',
], function () {
    Route::redirect(uri: 'settings', destination: 'edit');

    Route::get('/', [AccountProfileController::class, 'show'])
        ->name('account.profile');

    Route::get('donations', [AccountDonationController::class, 'index'])
        ->name('account.donations');

    Route::prefix('infractions')->group(function () {
        Route::get('/', [AccountInfractionsController::class, 'index'])
            ->name('account.infractions');

        Route::post('{warningId}/acknowledge', [AccountInfractionsController::class, 'acknowledgeWarning'])
            ->name('account.infractions.acknowledge');
    });

    Route::get('billing', [AccountBillingController::class, 'index'])
        ->name('account.billing');

    Route::prefix('edit')->group(function () {
        Route::get('/', [AccountSettingController::class, 'show'])
            ->name('account.settings');

        Route::post('email/verify', [AccountSettingController::class, 'sendVerificationEmail'])
            ->name('account.settings.email');

        Route::get('email/confirm', [AccountSettingController::class, 'showConfirmForm'])
            ->name('account.settings.email.confirm')
            ->middleware('signed');

        Route::post('password', [AccountSettingController::class, 'changePassword'])
            ->name('account.settings.password');

        Route::post('username', [AccountSettingController::class, 'changeUsername'])
            ->name('account.settings.username');
    });

    Route::prefix('games')->group(function () {
        Route::get('/', [AccountGameAccountController::class, 'index'])
            ->name('account.games');

        Route::delete('/{minecraft_player}', [AccountGameAccountController::class, 'destroy'])
            ->name('account.games.delete');
    });

    Route::prefix('security')->group(function () {
        Route::get('/', [AccountSecurityController::class, 'show'])
            ->name('account.security');

        Route::post('mfa', StartMfaController::class)
            ->name('account.security.start');

        Route::get('mfa/setup', SetupMfaController::class)
            ->name('account.security.setup');

        Route::post('mfa/finish', FinishMfaController::class)
            ->name('account.security.finish');

        Route::get('mfa/disable', [DisableMfaController::class, 'show'])
            ->name('account.security.disable')
            ->middleware('password.confirm');

        Route::delete('mfa/disable', [DisableMfaController::class, 'destroy'])
            ->name('account.security.disable.confirm')
            ->middleware('password.confirm');

        Route::get('mfa/backup', [ResetBackupController::class, 'show'])
            ->name('account.security.reset-backup')
            ->middleware('password.confirm');

        Route::post('mfa/backup', [ResetBackupController::class, 'update'])
            ->name('account.security.reset-backup.confirm')
            ->middleware('password.confirm');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('auth/minecraft/{token}', [MinecraftPlayerLinkController::class, 'index'])
        ->name('auth.minecraft.token');
});
