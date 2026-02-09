<?php

use App\Http\Controllers\Api\v3\Bans\PlayerBanController;
use App\Http\Controllers\Api\v3\Builds\MinecraftBuildController;
use App\Http\Controllers\Api\v3\Builds\MinecraftBuildNameController;
use App\Http\Controllers\Api\v3\Builds\MinecraftBuildVoteController;
use App\Http\Controllers\Api\v3\Players\Homes\MinecraftPlayerHomeController;
use App\Http\Controllers\Api\v3\Players\Homes\MinecraftPlayerHomeLimitController;
use App\Http\Controllers\Api\v3\Players\Homes\MinecraftPlayerHomeNameController;
use App\Http\Controllers\Api\v3\Players\MinecraftPlayerBanController;
use App\Http\Controllers\Api\v3\Players\MinecraftPlayerController;
use App\Http\Controllers\Api\v3\Players\MinecraftRegisterController as DeprecatedMinecraftRegisterController;
use App\Http\Controllers\Api\v3\Server\MinecraftConfigController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionAuthController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionEndController;
use App\Http\Controllers\Api\v3\Server\MinecraftRegisterController;
use App\Http\Controllers\Api\v3\Server\MinecraftStatsController;
use App\Http\Controllers\Api\v3\Warps\MinecraftWarpController;
use App\Http\Controllers\Api\v3\Warps\MinecraftWarpNameController;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.api_url'))->group(function () {
    Route::get('/', fn () => ['status' => 'ok']);

    Route::prefix('v3')->name('v3.')->group(function () {
        Route::prefix('server')
            ->middleware('require-server-token')
            ->group(function () {
                Route::post('connection/authorize', MinecraftConnectionAuthController::class);
                Route::post('connection/end', MinecraftConnectionEndController::class);
                Route::post('stats', [MinecraftStatsController::class, 'store']);
                Route::get('config', MinecraftConfigController::class);

                Route::post('register', [MinecraftRegisterController::class, 'store'])
                    ->middleware('throttle:3,1');

                Route::put('register', [MinecraftRegisterController::class, 'update'])
                    ->middleware('throttle:12,1');
            });

        Route::prefix('bans/uuid')
            ->middleware('require-server-token')
            ->group(function () {
                Route::post('/', [PlayerBanController::class, 'store']);
                Route::put('{banId}', [PlayerBanController::class, 'update']);
                Route::delete('{banId}', [PlayerBanController::class, 'delete']);
            });

        Route::prefix('builds')->group(function () {
            Route::get('/', [MinecraftBuildController::class, 'index']);
            Route::post('/', [MinecraftBuildController::class, 'store']);
            Route::get('names', [MinecraftBuildNameController::class, 'index']);

            Route::get('{build}', [MinecraftBuildController::class, 'show']);
            Route::put('{build}', [MinecraftBuildController::class, 'update'])
                ->middleware('require-server-token');
            Route::delete('{build}', [MinecraftBuildController::class, 'destroy']);
            Route::patch('{build}/set', [MinecraftBuildController::class, 'patch']);

            Route::post('{build}/vote', [MinecraftBuildVoteController::class, 'store']);
            Route::delete('{build}/vote', [MinecraftBuildVoteController::class, 'destroy']);
        });

        Route::prefix('warps')->group(function () {
            Route::get('/', [MinecraftWarpController::class, 'index']);
            Route::post('/', [MinecraftWarpController::class, 'store']);
            Route::get('names', [MinecraftWarpNameController::class, 'index']);
            Route::get('all', [MinecraftWarpController::class, 'bulk']);

            Route::get('{warp}', [MinecraftWarpController::class, 'show']);
            Route::put('{warp}', [MinecraftWarpController::class, 'update'])
                ->middleware('require-server-token');
            Route::delete('{warp}', [MinecraftWarpController::class, 'destroy'])
                ->middleware('require-server-token');
        });

        Route::prefix('players/{minecraft_uuid}')->group(function () {
            Route::get('/', [MinecraftPlayerController::class, 'show']);
            Route::patch('/', [MinecraftPlayerController::class, 'update'])
                ->middleware('require-server-token');

            Route::get('bans', [MinecraftPlayerBanController::class, 'index']);

            /** @deprecated */
            Route::post('register', [DeprecatedMinecraftRegisterController::class, 'store'])
                ->middleware('require-server-token', 'throttle:3,1');

            /** @deprecated */
            Route::put('register', [DeprecatedMinecraftRegisterController::class, 'update'])
                ->middleware('require-server-token', 'throttle:12,1');

            Route::prefix('homes')->group(function () {
                Route::get('/', [MinecraftPlayerHomeController::class, 'index']);
                Route::post('/', [MinecraftPlayerHomeController::class, 'store'])
                    ->middleware('require-server-token');
                Route::get('names', [MinecraftPlayerHomeNameController::class, 'index']);
                Route::get('limit', MinecraftPlayerHomeLimitController::class);

                Route::get('{home}', [MinecraftPlayerHomeController::class, 'show']);
                Route::put('{home}', [MinecraftPlayerHomeController::class, 'update'])
                    ->middleware('require-server-token');
                Route::delete('{home}', [MinecraftPlayerHomeController::class, 'destroy'])
                    ->middleware('require-server-token');
            });
        });
    });
});
