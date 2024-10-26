<?php

use App\Domains\Panel\Data\PanelGroupScope;
use App\Http\Controllers\Panel\AccountActivate;
use App\Http\Controllers\Panel\AccountApproveEmailChange;
use App\Http\Controllers\Panel\AccountController;
use App\Http\Controllers\Panel\AccountGameAccount;
use App\Http\Controllers\Panel\AccountResendActivation;
use App\Http\Controllers\Panel\AccountUpdateBadges;
use App\Http\Controllers\Panel\AccountUpdateGroups;
use App\Http\Controllers\Panel\ActivityController;
use App\Http\Controllers\Panel\BadgeController;
use App\Http\Controllers\Panel\BanAppealController;
use App\Http\Controllers\Panel\BuilderRanksController;
use App\Http\Controllers\Panel\DonationController;
use App\Http\Controllers\Panel\DonationPerksController;
use App\Http\Controllers\Panel\GameIPBanController;
use App\Http\Controllers\Panel\GamePlayerBanController;
use App\Http\Controllers\Panel\GroupAccountController;
use App\Http\Controllers\Panel\GroupController;
use App\Http\Controllers\Panel\MinecraftConfigController;
use App\Http\Controllers\Panel\MinecraftPlayerController;
use App\Http\Controllers\Panel\MinecraftPlayerLookupController;
use App\Http\Controllers\Panel\MinecraftPlayerReloadAliasController;
use App\Http\Controllers\Panel\PlayerWarningController;
use App\Http\Controllers\Panel\ServerController;
use App\Http\Controllers\Panel\ServerTokenController;
use App\Http\Controllers\Panel\ShowcaseWarpsController;
use Illuminate\Support\Facades\Route;

Route::name('front.panel.')
    ->prefix('panel')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        PanelGroupScope::ACCESS_PANEL->toMiddleware(),
        'require-mfa',
    ])
    ->group(function() {
        Route::view('/', 'admin.index')
            ->name('index');

        Route::resource('accounts', AccountController::class)
            ->except(['destroy', 'create'])
            ->middleware(PanelGroupScope::MANAGE_ACCOUNTS->toMiddleware());

        Route::group([
            'prefix' => 'accounts/{account}',
            'as' => 'accounts.',
            'middleware' => PanelGroupScope::MANAGE_ACCOUNTS->toMiddleware(),
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


        Route::get('minecraft/config', [MinecraftConfigController::class, 'create'])
            ->name('minecraft.config.create');
        Route::patch('minecraft/config', [MinecraftConfigController::class, 'update'])
            ->name('minecraft.config.update');

        Route::resource('minecraft-players', MinecraftPlayerController::class)
            ->except(['destroy'])
            ->middleware(PanelGroupScope::MANAGE_ACCOUNTS->toMiddleware());

        Route::post('minecraft-players/lookup', MinecraftPlayerLookupController::class)
            ->name('minecraft-players.lookup')
            ->middleware(PanelGroupScope::MANAGE_ACCOUNTS->toMiddleware());

        Route::resource('showcase-warps', ShowcaseWarpsController::class)
            ->middleware(PanelGroupScope::MANAGE_SHOWCASE_WARPS->toMiddleware());

        Route::resource('badges', BadgeController::class)
            ->middleware(PanelGroupScope::MANAGE_BADGES->toMiddleware());

        Route::resource('donations', DonationController::class)
            ->middleware(PanelGroupScope::MANAGE_DONATIONS->toMiddleware());

        Route::resource('donation-perks', DonationPerksController::class)
            ->except(['index', 'show'])
            ->middleware(PanelGroupScope::MANAGE_DONATIONS->toMiddleware());

        Route::resource('servers', ServerController::class)
            ->except(['show'])
            ->middleware(PanelGroupScope::MANAGE_SERVERS->toMiddleware());

        Route::resource('server-tokens', ServerTokenController::class)
            ->except(['show'])
            ->middleware(PanelGroupScope::MANAGE_SERVERS->toMiddleware());

        Route::resource('warnings', PlayerWarningController::class)
            ->middleware(PanelGroupScope::MANAGE_WARNINGS->toMiddleware());

        Route::resource('ip-bans', GameIPBanController::class)
            ->except(['show'])
            ->middleware(PanelGroupScope::MANAGE_BANS->toMiddleware());

        Route::resource('player-bans', GamePlayerBanController::class)
            ->except(['show'])
            ->middleware(PanelGroupScope::MANAGE_BANS->toMiddleware());

        Route::group([
            'prefix' => 'groups',
            'as' => 'groups.',
            'middleware' => PanelGroupScope::MANAGE_GROUPS->toMiddleware(),
        ], function () {
            Route::get('/', [GroupController::class, 'index'])
                ->name('index');

            Route::get('{group}/accounts', [GroupAccountController::class, 'index'])
                ->name('accounts');
        });

        Route::group([
            'prefix' => 'builder-ranks',
            'as' => 'builder-ranks.',
            'middleware' => PanelGroupScope::REVIEW_BUILD_RANK_APPS->toMiddleware(),
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
            ->only('index', 'show', 'update')
            ->middleware(PanelGroupScope::REVIEW_APPEALS->toMiddleware());

        Route::resource('activity', ActivityController::class)
            ->only(['index', 'show'])
            ->middleware(PanelGroupScope::VIEW_ACTIVITY->toMiddleware());
    });
