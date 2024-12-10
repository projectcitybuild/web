<?php

use App\Domains\Panel\Data\PanelGroupScope;
use App\Http\Controllers\Manage\AccountActivate;
use App\Http\Controllers\Manage\AccountApproveEmailChange;
use App\Http\Controllers\Manage\AccountController;
use App\Http\Controllers\Manage\AccountGameAccount;
use App\Http\Controllers\Manage\AccountResendActivation;
use App\Http\Controllers\Manage\AccountUpdateBadges;
use App\Http\Controllers\Manage\AccountUpdateGroups;
use App\Http\Controllers\Manage\ActivityController;
use App\Http\Controllers\Manage\BadgeController;
use App\Http\Controllers\Manage\BanAppealController;
use App\Http\Controllers\Manage\BuilderRanksController;
use App\Http\Controllers\Manage\DonationController;
use App\Http\Controllers\Manage\DonationPerksController;
use App\Http\Controllers\Manage\GameIPBanController;
use App\Http\Controllers\Manage\GamePlayerBanController;
use App\Http\Controllers\Manage\GroupAccountController;
use App\Http\Controllers\Manage\GroupController;
use App\Http\Controllers\Manage\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Manage\Minecraft\MinecraftPlayerController;
use App\Http\Controllers\Manage\Minecraft\MinecraftWarpController;
use App\Http\Controllers\Manage\MinecraftPlayerLookupController;
use App\Http\Controllers\Manage\PlayerWarningController;
use App\Http\Controllers\Manage\ServerController;
use App\Http\Controllers\Manage\ServerTokenController;
use App\Http\Controllers\Manage\ShowcaseWarpsController;
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

        Route::prefix('minecraft')->name('minecraft.')->group(function () {
            Route::get('config', [MinecraftConfigController::class, 'create'])
                ->name('config.create')
                ->middleware(PanelGroupScope::MANAGE_SHOWCASE_WARPS->toMiddleware());

            Route::patch('config', [MinecraftConfigController::class, 'update'])
                ->name('config.update')
                ->middleware(PanelGroupScope::MANAGE_SHOWCASE_WARPS->toMiddleware());

            Route::resource('warps', MinecraftWarpController::class)
                ->middleware(PanelGroupScope::MANAGE_SHOWCASE_WARPS->toMiddleware());
        });

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

        Route::resource('groups', GroupController::class)
            ->except(['show'])
            ->middleware(PanelGroupScope::MANAGE_GROUPS->toMiddleware());

        Route::get('{group}/accounts', [GroupAccountController::class, 'index'])
            ->name('groups.accounts')
            ->middleware(PanelGroupScope::MANAGE_GROUPS->toMiddleware());

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
