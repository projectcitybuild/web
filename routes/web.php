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
use App\Http\Controllers\Front\Auth\LoginController;
use App\Http\Controllers\Front\Auth\LogoutController;
use App\Http\Controllers\Front\Auth\Mfa\MfaRecoveryController;
use App\Http\Controllers\Front\Auth\Mfa\MfaLoginGateController;
use App\Http\Controllers\Front\Auth\PasswordResetController;
use App\Http\Controllers\Front\Auth\ReauthController;
use App\Http\Controllers\Front\Auth\RegisterController;
use App\Http\Controllers\Front\Auth\ActivateAccountController;
use App\Http\Controllers\Front\BanAppeal\BanAppealController;
use App\Http\Controllers\Front\BanAppeal\BanLookupController;
use App\Http\Controllers\Front\BanlistController;
use App\Http\Controllers\Front\BuilderRankApplicationController;
use App\Http\Controllers\Front\DonationController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\MinecraftPlayerLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('front.home');

Route::get('maps', fn () => view('front.pages.maps'))
    ->name('front.maps');

Route::prefix('donate')->group(function () {
    Route::get('/', [DonationController::class, 'index'])
        ->name('front.donate');

    Route::post('checkout', [DonationController::class, 'checkout'])
        ->name('front.donations.checkout');

    Route::get('success', [DonationController::class, 'success'])
        ->name('front.donate.success');
});

Route::prefix('rank-up')->group(function () {
    Route::get('/', [BuilderRankApplicationController::class, 'index'])
        ->name('front.rank-up');

    Route::post('/', [BuilderRankApplicationController::class, 'store'])
        ->name('front.rank-up.submit');

    Route::get('{id}', [BuilderRankApplicationController::class, 'show'])
        ->name('front.rank-up.status');
});

Route::prefix('appeal')->group(function () {
    Route::get('/', [BanAppealController::class, 'index'])
        ->name('front.appeal');

    Route::redirect('auth', '/appeal')
        ->name('front.appeal.auth')
        ->middleware('auth');

    Route::get('{banAppeal}', [BanAppealController::class, 'show'])
        ->name('front.appeal.show');
});

Route::prefix('bans')->group(function () {
    Route::get('/', [BanlistController::class, 'index'])
        ->name('front.banlist');

    Route::post('/', BanLookupController::class)
        ->name('front.bans.lookup');

    Route::get('{ban}/appeal', [BanAppealController::class, 'create'])
        ->name('front.appeal.create');

    Route::post('{ban}/appeal', [BanAppealController::class, 'store'])
        ->name('front.appeal.submit');
});

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'show'])
        ->name('front.login')
        ->middleware('guest');

    Route::post('/', [LoginController::class, 'login'])
        ->name('front.login.submit')
        ->middleware(['guest', 'throttle:login']);

    Route::get('reauth', [ReauthController::class, 'show'])
        ->name('front.password.confirm')
        ->middleware('auth');

    Route::post('reauth', [ReauthController::class, 'store'])
        ->name('front.password.confirm.submit')
        ->middleware(['auth', 'throttle:6,1']);
});

Route::get('logout', LogoutController::class)
    ->name('front.logout')
    ->middleware('auth');

Route::prefix('mfa')->group(function () {
    Route::get('/', [MfaLoginGateController::class, 'show'])
        ->name('front.login.mfa')
        ->middleware(['auth', 'active-mfa']);

    Route::post('/', [MfaLoginGateController::class, 'store'])
        ->name('front.login.mfa.submit')
        ->middleware(['auth', 'active-mfa', 'throttle:6,1']);

    Route::get('recover', [MfaRecoveryController::class, 'show'])
        ->name('front.login.mfa-recover')
        ->middleware(['auth', 'active-mfa']);

    Route::delete('recover', [MfaRecoveryController::class, 'destroy'])
        ->name('front.login.mfa-recover.submit')
        ->middleware(['auth', 'active-mfa', 'throttle:6,1']);
});

Route::prefix('password-reset')->group(function () {
    Route::get('/', [PasswordResetController::class, 'create'])
        ->name('front.password-reset.create');

    Route::post('/', [PasswordResetController::class, 'store'])
        ->name('front.password-reset.store');

    Route::get('edit', [PasswordResetController::class, 'edit'])
        ->name('front.password-reset.edit')
        ->middleware('signed');

    Route::patch('edit', [PasswordResetController::class, 'update'])
        ->name('front.password-reset.update');
});

Route::prefix('register')->group(function () {
    Route::get('/', [RegisterController::class, 'show'])
        ->name('front.register')
        ->middleware('guest');

    Route::post('/', [RegisterController::class, 'register'])
        ->name('front.register.submit')
        ->middleware('guest');
});

Route::prefix('activate')->group(function () {
    Route::get('/', [ActivateAccountController::class, 'show'])
        ->name('front.activate')
        ->middleware(['guest']);

    Route::get('verify', [ActivateAccountController::class, 'activate'])
        ->name('front.activate.verify')
        ->middleware(['signed', 'guest']);

    Route::post('resend', [ActivateAccountController::class, 'resendMail'])
        ->name('front.activate.resend')
        ->middleware(['guest', 'throttle:3,1']);
});

Route::group([
    'prefix' => 'account',
    'middleware' => ['auth', 'mfa'],
], function () {
    Route::redirect(uri: 'settings', destination: 'edit');

    Route::get('/', [AccountProfileController::class, 'show'])
        ->name('front.account.profile');

    Route::get('donations', [AccountDonationController::class, 'index'])
        ->name('front.account.donations');

    Route::prefix('infractions')->group(function () {
        Route::get('/', [AccountInfractionsController::class, 'index'])
            ->name('front.account.infractions');

        Route::post('{warningId}/acknowledge', [AccountInfractionsController::class, 'acknowledgeWarning'])
            ->name('front.account.infractions.acknowledge');
    });

    Route::get('billing', [AccountBillingController::class, 'index'])
        ->name('front.account.billing');

    Route::prefix('edit')->group(function () {
        Route::get('/', [AccountSettingController::class, 'show'])
            ->name('front.account.settings');

        Route::post('email/verify', [AccountSettingController::class, 'sendVerificationEmail'])
            ->name('front.account.settings.email');

        Route::get('email/confirm', [AccountSettingController::class, 'showConfirmForm'])
            ->name('front.account.settings.email.confirm')
            ->middleware('signed');

        Route::post('password', [AccountSettingController::class, 'changePassword'])
            ->name('front.account.settings.password');

        Route::post('username', [AccountSettingController::class, 'changeUsername'])
            ->name('front.account.settings.username');
    });

    Route::prefix('games')->group(function () {
        Route::get('/', [AccountGameAccountController::class, 'index'])
            ->name('front.account.games');

        Route::delete('/{minecraft_player}', [AccountGameAccountController::class, 'destroy'])
            ->name('front.account.games.delete');
    });

    Route::prefix('security')->group(function () {
        Route::get('/', [AccountSecurityController::class, 'show'])
            ->name('front.account.security');

        Route::post('mfa', StartMfaController::class)
            ->name('front.account.security.start');

        Route::get('mfa/setup', SetupMfaController::class)
            ->name('front.account.security.setup');

        Route::post('mfa/finish', FinishMfaController::class)
            ->name('front.account.security.finish');

        Route::get('mfa/disable', [DisableMfaController::class, 'show'])
            ->name('front.account.security.disable')
            ->middleware('password.confirm');

        Route::delete('mfa/disable', [DisableMfaController::class, 'destroy'])
            ->name('front.account.security.disable.confirm')
            ->middleware('password.confirm');

        Route::get('mfa/backup', [ResetBackupController::class, 'show'])
            ->name('front.account.security.reset-backup')
            ->middleware('password.confirm');

        Route::post('mfa/backup', [ResetBackupController::class, 'update'])
            ->name('front.account.security.reset-backup.confirm')
            ->middleware('password.confirm');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('auth/minecraft/{token}', [MinecraftPlayerLinkController::class, 'index'])
        ->name('front.auth.minecraft.token');
});
