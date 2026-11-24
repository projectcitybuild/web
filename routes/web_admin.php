<?php

use App\Http\Controllers\Admin\Auditing\ActivityController;
use App\Http\Controllers\Admin\Auditing\BackgroundJobController;
use App\Http\Controllers\Admin\Auditing\PlayerOpElevationController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountActivateController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountApproveEmailChangeController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountBadgeController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountBanAppealController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountBuilderRankApplicationsController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountDiscordController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountGameAccountController as ManageAccountGameAccountController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountMfaController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountPlayerController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountResendActivationController;
use App\Http\Controllers\Admin\Manage\Accounts\AccountRoleController;
use App\Http\Controllers\Admin\Manage\Badges\BadgeAccountsController;
use App\Http\Controllers\Admin\Manage\Badges\BadgeController;
use App\Http\Controllers\Admin\Manage\Bans\GameIPBanController;
use App\Http\Controllers\Admin\Manage\Bans\GamePlayerBanController;
use App\Http\Controllers\Admin\Manage\DiscordRoleMappingController;
use App\Http\Controllers\Admin\Manage\Donations\DonationController as ManageDonationController;
use App\Http\Controllers\Admin\Manage\Donations\DonationPerkController;
use App\Http\Controllers\Admin\Manage\HomeController as HomeIndexController;
use App\Http\Controllers\Admin\Manage\Homes\HomeController;
use App\Http\Controllers\Admin\Manage\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Admin\Manage\Minecraft\MinecraftWarpController;
use App\Http\Controllers\Admin\Manage\PassportClients\PassportClientController;
use App\Http\Controllers\Admin\Manage\Permissions\PermissionsController;
use App\Http\Controllers\Admin\Manage\PlayerMail\PlayerMailBroadcastController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerAliasRefreshController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerAuditController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerBanController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerHomeController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerIpController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerSessionController;
use App\Http\Controllers\Admin\Manage\Players\MinecraftPlayerWarningController;
use App\Http\Controllers\Admin\Manage\Roles\RoleAccountController;
use App\Http\Controllers\Admin\Manage\Roles\RoleController;
use App\Http\Controllers\Admin\Manage\Roles\RolePermissionController;
use App\Http\Controllers\Admin\Manage\Servers\ServerController;
use App\Http\Controllers\Admin\Manage\Servers\ServerTokenController;
use App\Http\Controllers\Admin\Manage\Warnings\PlayerWarningController;
use App\Http\Controllers\Front\PlayerReportController as FrontPlayerReportController;
use Illuminate\Support\Facades\Route;
use Inertia\EncryptHistoryMiddleware;

Route::name('manage.')
    ->prefix('manage')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-manage',
        'require-mfa',
        EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        Route::get('/', HomeIndexController::class)
            ->name('index');

        Route::resource('accounts', AccountController::class)
            ->except(['destroy']);

        Route::prefix('accounts/{account}')->group(function () {
            Route::post('discord/sync', [AccountDiscordController::class, 'sync'])
                ->name('accounts.discord.sync');
            Route::delete('discord', [AccountDiscordController::class, 'destroy'])
                ->name('accounts.discord.destroy');

            Route::post('activate', [AccountActivateController::class, 'update']);
            Route::delete('activate', [AccountActivateController::class, 'destroy']);
            Route::post('activate/send', AccountResendActivationController::class);

            Route::delete('mfa', [AccountMfaController::class, 'destroy']);

            Route::post('email-change/{accountEmailChange}/approve', AccountApproveEmailChangeController::class);
            Route::delete('player/{minecraftPlayer}', [ManageAccountGameAccountController::class, 'delete']);

            Route::get('roles', [AccountRoleController::class, 'index']);
            Route::put('roles', [AccountRoleController::class, 'update']);

            Route::get('badges', [AccountBadgeController::class, 'index']);
            Route::put('badges', [AccountBadgeController::class, 'update']);

            Route::get('players', [AccountPlayerController::class, 'create']);
            Route::post('players', [AccountPlayerController::class, 'store']);
            Route::delete('players/{player}', [AccountPlayerController::class, 'destroy']);

            Route::get('ban-appeals', [AccountBanAppealController::class, 'index']);

            Route::get('builder-ranks', [AccountBuilderRankApplicationsController::class, 'index']);
        });

        Route::resource('players', MinecraftPlayerController::class)
            ->except(['destroy']);

        Route::prefix('players/{player}')->group(function () {
            Route::get('bans', [MinecraftPlayerBanController::class, 'index']);
            Route::get('warnings', [MinecraftPlayerWarningController::class, 'index']);
            Route::get('ips', [MinecraftPlayerIpController::class, 'index']);
            Route::get('homes', [MinecraftPlayerHomeController::class, 'index']);
            Route::get('sessions', [MinecraftPlayerSessionController::class, 'index']);
            Route::get('audits', [MinecraftPlayerAuditController::class, 'index']);
            Route::post('alias/refresh', MinecraftPlayerAliasRefreshController::class);
        });

        Route::prefix('minecraft')->name('minecraft.')->group(function () {
            Route::get('config', [MinecraftConfigController::class, 'create'])
                ->name('config.create');

            Route::post('config', [MinecraftConfigController::class, 'store'])
                ->name('config.store');

            Route::resource('warps', MinecraftWarpController::class);
        });

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create']);
        Route::post('roles', [RoleController::class, 'store']);
        Route::get('roles/{role}/edit', [RoleController::class, 'edit']);
        Route::put('roles/{role}', [RoleController::class, 'update']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy']);
        Route::get('roles/{role}/accounts', [RoleAccountController::class, 'index']);
        Route::get('roles/{role}/permissions', [RolePermissionController::class, 'index']);
        Route::put('roles/{role}/permissions', [RolePermissionController::class, 'update']);

        Route::get('discord/role-mappings', [DiscordRoleMappingController::class, 'index'])
            ->name('discord-role-mappings.index');
        Route::post('discord/role-mappings', [DiscordRoleMappingController::class, 'store'])
            ->name('discord-role-mappings.store');
        Route::put('discord/role-mappings/{discordRoleMapping}', [DiscordRoleMappingController::class, 'update'])
            ->name('discord-role-mappings.update');
        Route::delete('discord/role-mappings/{discordRoleMapping}', [DiscordRoleMappingController::class, 'destroy'])
            ->name('discord-role-mappings.destroy');

        Route::get('permissions', [PermissionsController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionsController::class, 'create']);
        Route::post('permissions', [PermissionsController::class, 'store']);
        Route::get('permissions/{permission}/edit', [PermissionsController::class, 'edit']);
        Route::put('permissions/{permission}', [PermissionsController::class, 'update']);
        Route::delete('permissions/{permission}', [PermissionsController::class, 'destroy']);

        Route::get('player-mail', [PlayerMailBroadcastController::class, 'index'])
            ->name('player-mail.index');
        Route::get('player-mail/create', [PlayerMailBroadcastController::class, 'create'])
            ->name('player-mail.create');
        Route::post('player-mail', [PlayerMailBroadcastController::class, 'storeFromManage'])
            ->middleware('throttle:player-mail-send')
            ->name('player-mail.store');
        Route::get('player-mail/{broadcast}', [PlayerMailBroadcastController::class, 'show'])
            ->name('player-mail.show');
        Route::post('player-mail/broadcasts', [PlayerMailBroadcastController::class, 'store'])
            ->middleware('throttle:player-mail-send')
            ->name('player-mail.broadcasts.store');

        Route::get('homes', [HomeController::class, 'index'])->name('homes.index');
        Route::get('homes/create', [HomeController::class, 'create']);
        Route::post('homes', [HomeController::class, 'store']);
        Route::get('homes/{home}/edit', [HomeController::class, 'edit']);
        Route::put('homes/{home}', [HomeController::class, 'update']);
        Route::delete('homes/{home}', [HomeController::class, 'destroy']);

        Route::get('badges/{badge}/accounts', [BadgeAccountsController::class, 'index'])
            ->name('badges.accounts');
        Route::resource('badges', BadgeController::class);

        Route::resource('donations', ManageDonationController::class);

        Route::resource('donation-perks', DonationPerkController::class)
            ->except(['show']);

        Route::resource('servers', ServerController::class)
            ->except(['show']);

        Route::resource('server-tokens', ServerTokenController::class)
            ->except(['show']);

        Route::resource('passport-clients', PassportClientController::class)
            ->except(['show']);

        Route::post('passport-clients/{passport_client}/regenerate-secret', [PassportClientController::class, 'regenerateSecret'])
            ->name('passport-clients.regenerate-secret');

        Route::resource('warnings', PlayerWarningController::class);

        Route::resource('ip-bans', GameIPBanController::class)
            ->except(['show']);

        Route::resource('player-bans', GamePlayerBanController::class)
            ->except(['show']);
    });

Route::name('auditing.')
    ->prefix('audit')
    ->middleware([
        'auth',
        'activated',
        'mfa',
        'can:access-audit',
        'require-mfa',
        EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        Route::get('/', [ActivityController::class, 'index'])
            ->name('index');

        Route::resource('activity', ActivityController::class)
            ->only(['index']);

        Route::get('activity/audits/{audit}', [ActivityController::class, 'show'])
            ->name('activity.audit.show');

        Route::get('op-elevations', [PlayerOpElevationController::class, 'index'])
            ->name('op-elevations.index');

        Route::get('background-jobs', [BackgroundJobController::class, 'index'])
            ->name('background-jobs.index');

        Route::get('background-jobs/runs', [BackgroundJobController::class, 'runs'])
            ->name('background-jobs.runs');

        Route::post('background-jobs/scheduled-tasks/{taskId}/queue', [BackgroundJobController::class, 'queueTask'])
            ->name('background-jobs.scheduled-tasks.queue');

        Route::post('background-jobs/scheduled-tasks/{taskId}/run', [BackgroundJobController::class, 'runTask'])
            ->name('background-jobs.scheduled-tasks.run');

        Route::get('background-jobs/{run}', [BackgroundJobController::class, 'show'])
            ->name('background-jobs.show');
    });

Route::permanentRedirect('terms', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/terms-of-service')
    ->name('terms');

Route::permanentRedirect('privacy', 'https://portal.projectcitybuild.com/books/legal-and-compliance/page/privacy-policy')
    ->name('privacy');

Route::permanentRedirect('wiki', 'https://wiki.projectcitybuild.com')
    ->name('wiki');

Route::permanentRedirect('portal', 'https://portal.projectcitybuild.com/')
    ->name('portal');

Route::get('player-report', [FrontPlayerReportController::class, 'create'])
    ->name('report');
Route::post('player-report', [FrontPlayerReportController::class, 'store'])
    ->name('report.submit')
    ->middleware('throttle:5,1');
Route::get('report', [FrontPlayerReportController::class, 'create'])
    ->name('report.legacy');

Route::permanentRedirect('staff-apply', 'https://tally.so/r/yPMkZd')
    ->name('staff-apply');

Route::permanentRedirect('rules', 'https://portal.projectcitybuild.com/books/rules/page/community-rules')
    ->name('rules');

Route::permanentRedirect('ranks', 'https://portal.projectcitybuild.com/books/player-ranks/page/list-of-ranks')
    ->name('ranks');

Route::permanentRedirect('staff', 'https://portal.projectcitybuild.com/books/list-of-staff/page/active')
    ->name('staff');

Route::permanentRedirect('vote', 'https://portal.projectcitybuild.com/books/server-voting/page/server-voting')
    ->name('vote');

Route::permanentRedirect('map-archive', 'https://portal.projectcitybuild.com/books/map-archive/page/map-archive')
    ->name('map-archive');

Route::permanentRedirect('login', 'https://projectcitybuild.com/auth/login');

Route::permanentRedirect('register', 'https://projectcitybuild.com/auth/register');
