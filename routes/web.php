<?php

use App\Http\Controllers\BanlistController;
use App\Http\Controllers\BuilderRankApplicationController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MfaBackupController;
use App\Http\Controllers\MfaLoginGateController;
use App\Http\Controllers\MinecraftPlayerLinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReauthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Settings\AccountBillingController;
use App\Http\Controllers\Settings\AccountDonationController;
use App\Http\Controllers\Settings\AccountGameAccountController;
use App\Http\Controllers\Settings\AccountProfileController;
use App\Http\Controllers\Settings\AccountSecurityController;
use App\Http\Controllers\Settings\AccountSettingController;
use App\Http\Controllers\Settings\Mfa\DisableMfaController;
use App\Http\Controllers\Settings\Mfa\FinishMfaController;
use App\Http\Controllers\Settings\Mfa\ResetBackupController;
use App\Http\Controllers\Settings\Mfa\SetupMfaController;
use App\Http\Controllers\Settings\Mfa\StartMfaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Library\Environment\Environment;

// Force all web routes to load over https
if (Environment::isProduction()) {
    URL::forceScheme('https');
}

/*
|--------------------------------------------------------------------------
| Redirects
|--------------------------------------------------------------------------
|
| Permanent URL redirects
|
*/
Route::permanentRedirect('terms', 'https://forums.projectcitybuild.com/t/community-rules/22928')->name('terms');
Route::permanentRedirect('privacy', 'https://forums.projectcitybuild.com/privacy')->name('privacy');
Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')->name('wiki');
Route::permanentRedirect('maps', 'https://maps.pcbmc.co')->name('maps');
Route::permanentRedirect('3d-maps', 'https://3d.pcbmc.co')->name('3d-maps');
Route::permanentRedirect('report', 'https://forums.projectcitybuild.com/w/player-report')->name('report');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All available routes for the front-end
|
*/
Route::get('/', [HomeController::class, 'index'])
    ->name('front.home');

Route::get('bans', [BanlistController::class, 'index'])
    ->name('front.banlist');

Route::get('logout', [LogoutController::class, 'logout'])
    ->name('front.logout')
    ->middleware('auth');

Route::get('p/{name}', [PageController::class, 'index'])
    ->name('front.page');

Route::prefix('donate')->group(function () {
    Route::get('/', function () { abort(503); })
//    Route::get('/', [DonationController::class, 'index'])
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

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'show'])
        ->name('front.login')
        ->middleware('guest');

    Route::post('/', [LoginController::class, 'login'])
        ->name('front.login.submit')
        ->middleware('guest');

    Route::get('/reactivate', [LoginController::class, 'resendActivationEmail'])
        ->name('front.login.reactivate')
        ->middleware(['guest', 'throttle:3,1']);

    Route::get('/reauth', [ReauthController::class, 'show'])
        ->name('password.confirm')
        ->middleware('auth');

    Route::post('/reauth', [ReauthController::class, 'process'])
        ->name('password.confirm')
        ->middleware(['auth', 'throttle:6,1']);

    Route::get('/mfa', [MfaLoginGateController::class, 'create'])
        ->name('front.login.mfa')
        ->middleware(['auth', 'active-mfa']);

    Route::post('/mfa', [MfaLoginGateController::class, 'store'])
        ->name('front.login.mfa')
        ->middleware(['auth', 'active-mfa', 'throttle:6,1']);

    Route::get('/mfa/recover', [MfaBackupController::class, 'show'])
        ->name('front.login.mfa-recover')
        ->middleware(['auth', 'active-mfa']);

    Route::delete('/mfa/recover', [MfaBackupController::class, 'destroy'])
        ->name('front.login.mfa-recover')
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

    Route::get('activate', [RegisterController::class, 'activate'])
        ->name('front.register.activate')
        ->middleware(['signed', 'guest']);
});

Route::group([
    'prefix' => 'account',
    'middleware' => 'auth',
], function () {
    Route::redirect(uri: 'settings', destination: 'edit');

    Route::get('/', [AccountProfileController::class, 'show'])
        ->name('front.account.profile');

    Route::get('donations', [AccountDonationController::class, 'index'])
        ->name('front.account.donations');

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

        Route::post('/mfa', StartMfaController::class)
            ->name('front.account.security.start');

        Route::get('/mfa/setup', SetupMfaController::class)
            ->name('front.account.security.setup');

        Route::post('/mfa/finish', FinishMfaController::class)
            ->name('front.account.security.finish');

        Route::get('/mfa/disable', [DisableMfaController::class, 'show'])
            ->name('front.account.security.disable')
            ->middleware('password.confirm');

        Route::delete('/mfa/disable', [DisableMfaController::class, 'destroy'])
            ->name('front.account.security.disable')
            ->middleware('password.confirm');

        Route::get('/mfa/backup', [ResetBackupController::class, 'show'])
            ->name('front.account.security.reset-backup')
            ->middleware('password.confirm');

        Route::post('/mfa/backup', [ResetBackupController::class, 'update'])
            ->name('front.account.security.reset-backup')
            ->middleware('password.confirm');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('auth/minecraft/{token}', [MinecraftPlayerLinkController::class, 'index'])
        ->name('front.auth.minecraft.token');
});

Route::group([
    'prefix' => 'panel',
    'as' => 'front.panel.',
    'namespace' => 'Panel',
    'middleware' => ['auth', 'panel', 'requires-mfa']
], function () {
    Route::view('/', 'admin.index')->name('index');

    Route::resource('accounts', 'AccountController')->only(['index', 'show', 'edit', 'update']);
    Route::resource('donations', 'DonationController');
    Route::resource('donation-perks', 'DonationPerksController')->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('minecraft-players', 'MinecraftPlayerController')->except(['destroy']);
    Route::get('groups/{group}/accounts', 'GroupAccountController@index')->name('groups.accounts');
    Route::get('groups', 'GroupController@index')->name('groups.index');
    Route::resource('pages', 'PageController');

    Route::group(['prefix' => 'builder-ranks'], function () {
        Route::get('/', 'BuilderRanksController@index')
            ->name('builder-ranks.index');

        Route::get('{id}', 'BuilderRanksController@show')
            ->name('builder-ranks.show');

        Route::post('{id}/approve', 'BuilderRanksController@approve')
            ->name('builder-ranks.approve');

        Route::post('{id}/deny', 'BuilderRanksController@deny')
            ->name('builder-ranks.deny');
    });

    Route::post('minecraft-players/lookup', [
        'as' => 'minecraft-players.lookup',
        'uses' => 'MinecraftPlayerLookupController',
    ]);

    Route::post('minecraft-players/{minecraft_player}/reload-alias', [
        'as' => 'minecraft-players.reload-alias',
        'uses' => 'MinecraftPlayerReloadAliasController',
    ]);

    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('accounts', [
            'as' => 'account-search',
            'uses' => 'Api\\AccountSearchController',
        ]);
    });

    Route::group(['prefix' => 'accounts/{account}', 'as' => 'accounts.'], function () {
        Route::post('activate', [
            'as' => 'activate',
            'uses' => 'AccountActivate',
        ]);

        Route::post('resend-activation', [
            'as' => 'resend-activation',
            'uses' => 'AccountResendActivation',
        ]);

        Route::post('email-change/{accountEmailChange}/approve', [
            'as' => 'email-change.approve',
            'uses' => 'AccountApproveEmailChange',
        ]);

        Route::delete('game-account/{minecraftPlayer}', [
            'as' => 'game-account.delete',
            'uses' => 'AccountGameAccount@delete',
        ]);

        Route::post('update-groups', [
            'as' => 'update-groups',
            'uses' => 'AccountUpdateGroups',
        ]);
    });
});

if (Environment::isLocalDev()) {
    Route::view('ui', 'stylesheet');
}

if (! Environment::isProduction()) {
    Route::get('signed', fn () => 'test')
        ->name('test-signed')
        ->middleware('signed');
}
