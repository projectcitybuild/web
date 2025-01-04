<?php

use App\Http\Controllers\Manage\Accounts\AccountActivateController;
use App\Http\Controllers\Manage\Accounts\AccountApproveEmailChangeController;
use App\Http\Controllers\Manage\Accounts\AccountBadgeController;
use App\Http\Controllers\Manage\Accounts\AccountController;
use App\Http\Controllers\Manage\Accounts\AccountGameAccountController;
use App\Http\Controllers\Manage\Accounts\AccountGroupController;
use App\Http\Controllers\Manage\Accounts\AccountResendActivationController;
use App\Http\Controllers\Manage\Activity\ActivityController;
use App\Http\Controllers\Manage\Badges\BadgeController;
use App\Http\Controllers\Manage\Bans\GameIPBanController;
use App\Http\Controllers\Manage\Bans\GamePlayerBanController;
use App\Http\Controllers\Manage\Donations\DonationController;
use App\Http\Controllers\Manage\Donations\DonationPerksController;
use App\Http\Controllers\Manage\Groups\GroupAccountController;
use App\Http\Controllers\Manage\Groups\GroupController;
use App\Http\Controllers\Manage\HomeController;
use App\Http\Controllers\Manage\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Manage\Minecraft\MinecraftWarpController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerAliasRefreshController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerBanController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerController;
use App\Http\Controllers\Manage\Players\MinecraftPlayerWarningController;
use App\Http\Controllers\Manage\Servers\ServerController;
use App\Http\Controllers\Manage\Servers\ServerTokenController;
use App\Http\Controllers\Manage\Warnings\PlayerWarningController;
use Illuminate\Support\Facades\Route;

Route::name('manage.')
    ->prefix('manage')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-manage',
        'require-mfa',
        Inertia\EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        Route::get('/', HomeController::class)
            ->name('index');

        Route::resource('accounts', AccountController::class)
            ->except(['destroy', 'create']);

        Route::prefix('accounts/{account}')->group(function () {
            Route::post('activate', AccountActivateController::class);
            Route::post('resend-activation', AccountResendActivationController::class);
            Route::post('email-change/{accountEmailChange}/approve', AccountApproveEmailChangeController::class);
            Route::delete('player/{minecraftPlayer}', [AccountGameAccountController::class, 'delete']);

            Route::get('groups', [AccountGroupController::class, 'index']);
            Route::put('groups', [AccountGroupController::class, 'update']);

            Route::get('badges', [AccountBadgeController::class, 'index']);
            Route::put('badges', [AccountBadgeController::class, 'update']);
        });

        Route::resource('players', MinecraftPlayerController::class)
            ->except(['destroy']);

        Route::prefix('players/{player}')->group(function () {
            Route::get('bans', [MinecraftPlayerBanController::class, 'index']);
            Route::get('warnings', [MinecraftPlayerWarningController::class, 'index']);
            Route::post('alias/refresh', MinecraftPlayerAliasRefreshController::class);
        });

        Route::prefix('minecraft')->name('minecraft.')->group(function () {
            Route::get('config', [MinecraftConfigController::class, 'create']);
            Route::patch('config', [MinecraftConfigController::class, 'update']);
            Route::resource('warps', MinecraftWarpController::class);
        });

        Route::resource('groups', GroupController::class)
            ->except(['show']);

        Route::get('groups/{group}/accounts', [GroupAccountController::class, 'index']);

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

        Route::resource('activity', ActivityController::class)
            ->only(['index', 'show']);
    });
