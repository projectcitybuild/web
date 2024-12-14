<?php

use App\Domains\Manage\Data\PanelGroupScope;
use App\Http\Controllers\Manage\Accounts\AccountActivate;
use App\Http\Controllers\Manage\Accounts\AccountApproveEmailChange;
use App\Http\Controllers\Manage\Accounts\AccountController;
use App\Http\Controllers\Manage\Accounts\AccountGameAccount;
use App\Http\Controllers\Manage\Accounts\AccountResendActivation;
use App\Http\Controllers\Manage\Accounts\AccountUpdateBadges;
use App\Http\Controllers\Manage\Accounts\AccountUpdateGroups;
use App\Http\Controllers\Manage\ActivityController;
use App\Http\Controllers\Manage\Badges\BadgeController;
use App\Http\Controllers\Manage\BanAppeals\BanAppealController;
use App\Http\Controllers\Manage\Bans\GameIPBanController;
use App\Http\Controllers\Manage\Bans\GamePlayerBanController;
use App\Http\Controllers\Manage\BuilderRanksController;
use App\Http\Controllers\Manage\Donations\DonationController;
use App\Http\Controllers\Manage\Donations\DonationPerksController;
use App\Http\Controllers\Manage\Groups\GroupAccountController;
use App\Http\Controllers\Manage\Groups\GroupController;
use App\Http\Controllers\Manage\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Manage\Minecraft\MinecraftWarpController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerLookupController;
use App\Http\Controllers\Manage\Servers\ServerController;
use App\Http\Controllers\Manage\Servers\ServerTokenController;
use App\Http\Controllers\Manage\ShowcaseWarpsController;
use App\Http\Controllers\Manage\Warnings\PlayerWarningController;
use Illuminate\Support\Facades\Route;

Route::name('manage.')
    ->prefix('manage')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        PanelGroupScope::ACCESS_PANEL->toMiddleware(),
        'require-mfa',
    ])
    ->group(function() {
        Route::view('/', 'manage.pages.index')
            ->name('index');

        Route::resource('accounts', AccountController::class)
            ->except(['destroy', 'create']);

        Route::group([
            'prefix' => 'accounts/{account}',
            'as' => 'accounts.',
        ], function () {
            Route::post('activate', AccountActivate::class)
                ->name('activate');

            Route::post('resend-activation', AccountResendActivation::class)
                ->name('resend-activation');

            Route::post('email-change/{accountEmailChange}/approve', AccountApproveEmailChange::class)
                ->name('email-change.approve');

            Route::delete('game-account/{minecraftPlayer}', [AccountGameAccount::class, 'delete'])
                ->name('game-account.delete');

            Route::post('update-groups', AccountUpdateGroups::class)
                ->name('update-groups');

            Route::post('update-badges', AccountUpdateBadges::class)
                ->name('update-badges');
        });

        Route::prefix('minecraft')->name('minecraft.')->group(function () {
            Route::get('config', [MinecraftConfigController::class, 'create'])
                ->name('config.create');

            Route::patch('config', [MinecraftConfigController::class, 'update'])
                ->name('config.update');

            Route::resource('warps', MinecraftWarpController::class);
        });

        Route::resource('minecraft-players', MinecraftPlayerController::class)
            ->except(['destroy']);

        Route::post('minecraft-players/lookup', MinecraftPlayerLookupController::class)
            ->name('minecraft-players.lookup');

        Route::resource('showcase-warps', ShowcaseWarpsController::class);

        Route::resource('badges', BadgeController::class);

        Route::resource('donations', DonationController::class);

        Route::resource('donation-perks', DonationPerksController::class)
            ->except(['index', 'show']);

        Route::resource('servers', ServerController::class)
            ->except(['show']);

        Route::resource('server-tokens', ServerTokenController::class)
            ->except(['show']);

        Route::resource('warnings', PlayerWarningController::class);

        Route::resource('ip-bans', GameIPBanController::class)
            ->except(['show']);

        Route::resource('player-bans', GamePlayerBanController::class)
            ->except(['show']);

        Route::resource('groups', GroupController::class)
            ->except(['show']);

        Route::get('{group}/accounts', [GroupAccountController::class, 'index'])
            ->name('groups.accounts');

        Route::group([
            'prefix' => 'builder-ranks',
            'as' => 'builder-ranks.',
        ], function () {
            Route::get('/', [BuilderRanksController::class, 'index'])
                ->name('index');

            Route::get('{id}', [BuilderRanksController::class, 'show'])
                ->name('show');

            Route::post('{id}/approve', [BuilderRanksController::class, 'approve'])
                ->name('approve');

            Route::post('{id}/deny', [BuilderRanksController::class, 'deny'])
                ->name('deny');
        });

        Route::resource('ban-appeals', BanAppealController::class)
            ->only('index', 'show', 'update');

        Route::resource('activity', ActivityController::class)
            ->only(['index', 'show'])
            ->middleware(PanelGroupScope::VIEW_ACTIVITY->toMiddleware());
    });
