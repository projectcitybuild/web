<?php

use App\Http\Controllers\Panel\AccountActivate;
use App\Http\Controllers\Panel\AccountApproveEmailChange;
use App\Http\Controllers\Panel\AccountController;
use App\Http\Controllers\Panel\AccountGameAccount;
use App\Http\Controllers\Panel\AccountResendActivation;
use App\Http\Controllers\Panel\AccountUpdateGroups;
use App\Http\Controllers\Panel\Api\AccountSearchController;
use App\Http\Controllers\Panel\BanAppealController;
use App\Http\Controllers\Panel\BuilderRanksController;
use App\Http\Controllers\Panel\DonationController;
use App\Http\Controllers\Panel\DonationPerksController;
use App\Http\Controllers\Panel\GroupAccountController;
use App\Http\Controllers\Panel\GroupController;
use App\Http\Controllers\Panel\MinecraftPlayerController;
use App\Http\Controllers\Panel\MinecraftPlayerLookupController;
use App\Http\Controllers\Panel\MinecraftPlayerReloadAliasController;
use App\Http\Controllers\Panel\PageController;
use App\Http\Controllers\Panel\ServerController;
use App\Http\Controllers\Panel\ServerTokenController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'panel',
    'as' => 'front.panel.',
    'middleware' => ['auth', 'panel', 'requires-mfa']
], function () {
    Route::view('/', 'admin.index')
        ->name('index');

    Route::resource('accounts', AccountController::class)
        ->except(['destroy', 'create'])
        ->middleware('can:panel-manage-accounts');

    Route::resource('donations', DonationController::class);

    Route::resource('donation-perks', DonationPerksController::class)
        ->except(['index', 'show']);

    Route::resource('minecraft-players', MinecraftPlayerController::class)
        ->except(['destroy']);

    Route::resource('servers', ServerController::class)
        ->except(['show']);

    Route::resource('server-tokens', ServerTokenController::class)
        ->except(['show']);

    Route::get('groups/{group}/accounts', [GroupAccountController::class, 'index'])
        ->name('groups.accounts');

    Route::get('groups', [GroupController::class, 'index'])
        ->name('groups.index');

    Route::resource('pages', PageController::class);

    Route::group(['prefix' => 'builder-ranks'], function () {
        Route::get('/', [BuilderRanksController::class, 'index'])
            ->name('builder-ranks.index');

        Route::get('{id}', [BuilderRanksController::class, 'show'])
            ->name('builder-ranks.show');

        Route::post('{id}/approve', [BuilderRanksController::class, 'approve'])
            ->name('builder-ranks.approve');

        Route::post('{id}/deny', [BuilderRanksController::class, 'deny'])
            ->name('builder-ranks.deny');
    });

    Route::resource('ban-appeals', BanAppealController::class)
        ->only('index', 'show', 'update');

    Route::post('minecraft-players/lookup', MinecraftPlayerLookupController::class)
        ->name('minecraft-players.lookup');

    Route::post('minecraft-players/{minecraft_player}/reload-alias', MinecraftPlayerReloadAliasController::class)
        ->name('minecraft-players.reload-alias');

    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('accounts', AccountSearchController::class)
            ->name('account-search');
    });

    Route::group(['prefix' => 'accounts/{account}', 'as' => 'accounts.'], function () {
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
    });
});
