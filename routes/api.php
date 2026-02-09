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
use App\Http\Controllers\Api\v3\Players\MinecraftRegisterController;
use App\Http\Controllers\Api\v3\Server\MinecraftConfigController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionAuthController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionEndController;
use App\Http\Controllers\Api\v3\Server\MinecraftStatsController;
use App\Http\Controllers\Api\v3\Warps\MinecraftWarpController;
use App\Http\Controllers\Api\v3\Warps\MinecraftWarpNameController;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.api_url'))->group(function () {
    Route::get('/', fn () => ['status' => 'ok']);

    Route::prefix('v3')
        ->name('v3.')
        ->middleware('require-server-token')
        ->group(function () {
            Route::prefix('server')->group(function () {
                Route::post('connection/authorize', MinecraftConnectionAuthController::class);
                Route::post('connection/end', MinecraftConnectionEndController::class);
                Route::post('stats', [MinecraftStatsController::class, 'store']);
                Route::get('config', MinecraftConfigController::class);
            });

            Route::prefix('bans')->group(function () {
                Route::prefix('uuid')->group(function () {
                    Route::post('/', [PlayerBanController::class, 'store']);
                    Route::put('{banId}', [PlayerBanController::class, 'update']);
                    Route::delete('{banId}', [PlayerBanController::class, 'delete']);
                });
            });

            Route::prefix('builds')->group(function () {
                Route::get('/', [MinecraftBuildController::class, 'index']);
                Route::post('/', [MinecraftBuildController::class, 'store']);
                Route::get('names', [MinecraftBuildNameController::class, 'index']);

                Route::prefix('{build}')->group(function () {
                    Route::get('/', [MinecraftBuildController::class, 'show']);
                    Route::put('/', [MinecraftBuildController::class, 'update']);
                    Route::delete('/', [MinecraftBuildController::class, 'destroy']);
                    Route::patch('set', [MinecraftBuildController::class, 'patch']);

                    Route::post('vote', [MinecraftBuildVoteController::class, 'store']);
                    Route::delete('vote', [MinecraftBuildVoteController::class, 'destroy']);
                });
            });

            Route::prefix('warps')->group(function () {
                Route::get('/', [MinecraftWarpController::class, 'index']);
                Route::post('/', [MinecraftWarpController::class, 'store']);
                Route::get('names', [MinecraftWarpNameController::class, 'index']);
                Route::get('all', [MinecraftWarpController::class, 'bulk']);

                Route::prefix('{warp}')->group(function () {
                    Route::get('/', [MinecraftWarpController::class, 'show']);
                    Route::put('/', [MinecraftWarpController::class, 'update']);
                    Route::delete('/', [MinecraftWarpController::class, 'destroy']);
                });
            });

            Route::prefix('players/{minecraft_uuid}')->group(function () {
                Route::get('/', [MinecraftPlayerController::class, 'show']);
                Route::patch('/', [MinecraftPlayerController::class, 'update']);

                Route::get('bans', [MinecraftPlayerBanController::class, 'index']);

                Route::prefix('register')->group(function () {
                    Route::post('/', [MinecraftRegisterController::class, 'store'])
                        ->middleware('throttle:3,1');

                    Route::put('/', [MinecraftRegisterController::class, 'update'])
                        ->middleware('throttle:12,1');
                });

                Route::prefix('homes')->group(function () {
                    Route::get('/', [MinecraftPlayerHomeController::class, 'index']);
                    Route::post('/', [MinecraftPlayerHomeController::class, 'store']);
                    Route::get('names', [MinecraftPlayerHomeNameController::class, 'index']);
                    Route::get('limit', MinecraftPlayerHomeLimitController::class);

                    Route::prefix('{home}')->group(function () {
                        Route::get('/', [MinecraftPlayerHomeController::class, 'show']);
                        Route::put('/', [MinecraftPlayerHomeController::class, 'update']);
                        Route::delete('/', [MinecraftPlayerHomeController::class, 'destroy']);
                    });
                });
            });
        });
});
