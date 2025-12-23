<?php

use App\Http\Controllers\Front\Account\AccountBillingController;
use App\Http\Controllers\Front\Account\AccountDonationController;
use App\Http\Controllers\Front\Account\AccountGameAccountController;
use App\Http\Controllers\Front\Account\AccountProfileController;
use App\Http\Controllers\Front\Account\AccountRecordsController;
use App\Http\Controllers\Front\Account\Settings\MfaDisableController;
use App\Http\Controllers\Front\Account\Settings\MfaFinishController;
use App\Http\Controllers\Front\Account\Settings\MfaResetBackupController;
use App\Http\Controllers\Front\Account\Settings\MfaSetupController;
use App\Http\Controllers\Front\Account\Settings\MfaStartController;
use App\Http\Controllers\Front\Account\Settings\MfaStatusController;
use App\Http\Controllers\Front\Account\Settings\UpdateEmailController;
use App\Http\Controllers\Front\Account\Settings\UpdatePasswordController;
use App\Http\Controllers\Front\Account\Settings\UpdateUsernameController;
use App\Http\Controllers\Front\Auth\ActivateAccountController;
use App\Http\Controllers\Front\Auth\LoginController;
use App\Http\Controllers\Front\Auth\LogoutController;
use App\Http\Controllers\Front\Auth\Mfa\MfaLoginGateController;
use App\Http\Controllers\Front\Auth\Mfa\MfaRecoveryController;
use App\Http\Controllers\Front\Auth\PasswordResetController;
use App\Http\Controllers\Front\Auth\ReauthController;
use App\Http\Controllers\Front\Auth\RegisterController;
use App\Http\Controllers\Front\BanAppeal\BanAppealController;
use App\Http\Controllers\Front\BanAppeal\BanAppealFormController;
use App\Http\Controllers\Front\BanAppeal\BanAppealSearchController;
use App\Http\Controllers\Front\BanlistController;
use App\Http\Controllers\Front\BuilderRankApplicationController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\DonationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'front.pages.home.index')
    ->name('front.home');

Route::get('maps', fn () => view('front.pages.maps'))
    ->name('front.maps');

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactController::class, 'index'])
        ->name('front.contact');

    Route::post('/', [ContactController::class, 'store'])
        ->name('front.contact.submit')
        ->middleware('throttle:10,1');
});

Route::prefix('donate')->group(function () {
    Route::get('/', [DonationController::class, 'index'])
        ->name('front.donate');

    Route::post('checkout', [DonationController::class, 'checkout'])
        ->name('front.donations.checkout');

    Route::get('success', [DonationController::class, 'success'])
        ->name('front.donate.success');
});

Route::group([
    'prefix' => 'rank-up',
    'middleware' => ['auth', 'activated', 'mfa'],
], function () {
    Route::get('/', [BuilderRankApplicationController::class, 'create'])
        ->name('front.rank-up');

    Route::post('/', [BuilderRankApplicationController::class, 'store'])
        ->name('front.rank-up.submit');

    Route::get('{application}', [BuilderRankApplicationController::class, 'show'])
        ->name('front.rank-up.status');
});

Route::prefix('appeal')->group(function () {
    Route::view('/', 'front.pages.ban-appeal.index')
        ->name('front.appeal');

    Route::get('search', [BanAppealSearchController::class, 'index'])
        ->name('front.appeal.search')
        ->middleware('throttle:15,1');

    Route::prefix('create')->group(function () {
        Route::get('/', [BanAppealFormController::class, 'index'])
            ->name('front.appeal.form');

        Route::post('/', [BanAppealFormController::class, 'store'])
            ->name('front.appeal.form.submit')
            ->middleware('throttle:8,1');

        Route::get('{ban}', [BanAppealFormController::class, 'show'])
            ->name('front.appeal.form.prefilled');
    });

    Route::get('{banAppeal}', [BanAppealController::class, 'show'])
        ->name('front.appeal.show');
});

Route::prefix('bans')->group(function () {
    Route::get('/', [BanlistController::class, 'index'])
        ->name('front.banlist');

    Route::get('{ban}', [BanlistController::class, 'show'])
        ->name('front.bans.details');

    Route::get('{ban}/appeal', [BanAppealFormController::class, 'create'])
        ->name('front.appeal.create');

    Route::post('{ban}/appeal', [BanAppealFormController::class, 'store'])
        ->name('front.appeal.submit')
        ->middleware('throttle:8,1');
});

Route::prefix('auth')->group(function () {
    Route::prefix('login')->group(function () {
        Route::get('/', [LoginController::class, 'show'])
            ->name('front.login')
            ->middleware(['guest']);

        Route::post('/', [LoginController::class, 'login'])
            ->name('front.login.submit')
            ->middleware(['guest', 'throttle:login']);
    });

    Route::get('logout', LogoutController::class)
        ->name('front.logout')
        ->middleware(['auth']);

    Route::prefix('confirm')->group(function () {
        Route::get('/', [ReauthController::class, 'show'])
            ->name('front.password.confirm')
            ->middleware(['auth']);

        Route::post('/', [ReauthController::class, 'store'])
            ->name('front.password.confirm.submit')
            ->middleware(['auth', 'throttle:6,1']);
    });

    Route::prefix('mfa')->group(function () {
        Route::get('/', [MfaLoginGateController::class, 'show'])
            ->name('front.login.mfa')
            ->middleware(['auth', 'activated', 'active-mfa']);

        Route::post('/', [MfaLoginGateController::class, 'store'])
            ->name('front.login.mfa.submit')
            ->middleware(['auth', 'activated', 'active-mfa', 'throttle:6,1']);

        Route::get('recover', [MfaRecoveryController::class, 'show'])
            ->name('front.login.mfa-recover')
            ->middleware(['auth', 'activated', 'active-mfa']);

        Route::delete('recover', [MfaRecoveryController::class, 'destroy'])
            ->name('front.login.mfa-recover.submit')
            ->middleware(['auth', 'activated', 'active-mfa', 'throttle:6,1']);
    });

    Route::prefix('password-reset')->group(function () {
        Route::get('/', [PasswordResetController::class, 'create'])
            ->name('front.password-reset.create')
            ->middleware(['guest']);

        Route::post('/', [PasswordResetController::class, 'store'])
            ->name('front.password-reset.store')
            ->middleware(['guest']);

        Route::get('edit', [PasswordResetController::class, 'edit'])
            ->name('front.password-reset.edit')
            ->middleware(['signed', 'guest']);

        Route::patch('edit', [PasswordResetController::class, 'update'])
            ->name('front.password-reset.update')
            ->middleware(['guest']);
    });

    Route::prefix('register')->group(function () {
        Route::get('/', [RegisterController::class, 'show'])
            ->name('front.register')
            ->middleware(['guest']);

        Route::post('/', [RegisterController::class, 'store'])
            ->name('front.register.submit')
            ->middleware(['guest']);
    });

    Route::prefix('activate')->group(function () {
        Route::get('/', [ActivateAccountController::class, 'show'])
            ->name('front.activate')
            ->middleware(['auth', 'not-activated']);

        Route::get('verify/{token}', [ActivateAccountController::class, 'activate'])
            ->name('front.activate.verify')
            ->middleware(['auth', 'not-activated', 'signed']);

        Route::post('resend', [ActivateAccountController::class, 'resendMail'])
            ->name('front.activate.resend')
            ->middleware(['auth', 'not-activated', 'throttle:3,1']);
    });
});

Route::group([
    'prefix' => 'account',
    'middleware' => ['auth', 'activated', 'mfa'],
], function () {
    Route::redirect(uri: 'settings', destination: 'settings/email')
        ->name('front.account.settings');

    Route::get('/', [AccountProfileController::class, 'show'])
        ->name('front.account.profile');

    Route::get('donations', [AccountDonationController::class, 'index'])
        ->name('front.account.donations');

    Route::get('records', [AccountRecordsController::class, 'index'])
        ->name('front.account.records');

    Route::prefix('games')->group(function () {
        Route::get('/', [AccountGameAccountController::class, 'index'])
            ->name('front.account.games');

        Route::delete('/{minecraft_player}', [AccountGameAccountController::class, 'destroy'])
            ->name('front.account.games.delete');
    });

    Route::prefix('settings')->group(function () {
        Route::prefix('username')->group(function () {
            Route::get('/', [UpdateUsernameController::class, 'show'])
                ->name('front.account.settings.username');

            Route::post('/', [UpdateUsernameController::class, 'store'])
                ->name('front.account.settings.username.store');
        });

        Route::prefix('password')->group(function () {
            Route::get('/', [UpdatePasswordController::class, 'show'])
                ->name('front.account.settings.password');

            Route::post('/', [UpdatePasswordController::class, 'store'])
                ->name('front.account.settings.password.store');
        });

        Route::prefix('email')->group(function () {
            Route::get('/', [UpdateEmailController::class, 'show'])
                ->name('front.account.settings.email');

            Route::post('/', [UpdateEmailController::class, 'store'])
                ->name('front.account.settings.email.verify');

            Route::get('update', [UpdateEmailController::class, 'update'])
                ->name('front.account.settings.email.update')
                ->middleware('signed');
        });

        Route::prefix('mfa')->group(function () {
            Route::get('/', [MfaStatusController::class, 'show'])
                ->name('front.account.settings.mfa');

            Route::post('/', MfaStartController::class)
                ->name('front.account.settings.mfa.start');

            Route::get('setup', MfaSetupController::class)
                ->name('front.account.settings.mfa.setup');

            Route::post('finish', MfaFinishController::class)
                ->name('front.account.settings.mfa.finish');

            Route::get('disable', [MfaDisableController::class, 'show'])
                ->name('front.account.settings.mfa.disable')
                ->middleware('password.confirm');

            Route::delete('disable', [MfaDisableController::class, 'destroy'])
                ->name('front.account.settings.mfa.disable.confirm')
                ->middleware('password.confirm');

            Route::get('backup', [MfaResetBackupController::class, 'show'])
                ->name('front.account.settings.mfa.reset-backup')
                ->middleware('password.confirm');

            Route::post('backup', [MfaResetBackupController::class, 'update'])
                ->name('front.account.settings.mfa.reset-backup.confirm')
                ->middleware('password.confirm');
        });

        Route::get('billing', [AccountBillingController::class, 'index'])
            ->name('front.account.settings.billing');
    });
});
