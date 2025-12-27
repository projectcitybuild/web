<?php

use App\Http\Controllers\Api\v2\GamePlayerBanController;
use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildController;
use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildNameController;
use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildVoteController;
use App\Http\Controllers\Api\v2\Minecraft\MinecraftTelemetryController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeLimitController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeNameController;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftPlayerBanController;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftPlayerController as MinecraftPlayerControllerV2;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftRegisterController;
use App\Http\Controllers\Api\v2\Minecraft\Warps\MinecraftWarpController;
use App\Http\Controllers\Api\v2\Minecraft\Warps\MinecraftWarpNameController;
use App\Http\Controllers\Api\v3\Players\MinecraftPlayerController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionAuthController;
use App\Http\Controllers\Api\v3\Server\MinecraftConnectionEndController;
use App\Http\Controllers\Api\v3\Server\MinecraftConfigController;
use Illuminate\Support\Facades\Route;

/** @deprecated */
Route::prefix('api/v2')
    ->name('v2.')
    ->middleware('require-server-token')
    ->group(function () {
        Route::prefix('bans')->group(function () {
            Route::prefix('uuid')->group(function () {
                Route::post('/', [GamePlayerBanController::class, 'store']);
                Route::put('{banId}', [GamePlayerBanController::class, 'update']);
                Route::delete('{banId}', [GamePlayerBanController::class, 'delete']);
            });
        });

        Route::prefix('minecraft')->group(function () {
            Route::get('config', MinecraftConfigController::class);

            Route::prefix('warp')->group(function () {
                Route::get('/', [MinecraftWarpController::class, 'index']);
                Route::post('/', [MinecraftWarpController::class, 'store']);
                Route::get('name', [MinecraftWarpNameController::class, 'index']);
                Route::get('all', [MinecraftWarpController::class, 'bulk']);

                Route::prefix('{warp}')->group(function () {
                    Route::get('/', [MinecraftWarpController::class, 'show']);
                    Route::put('/', [MinecraftWarpController::class, 'update']);
                    Route::delete('/', [MinecraftWarpController::class, 'destroy']);
                });
            });

            Route::prefix('build')->group(function () {
                Route::get('/', [MinecraftBuildController::class, 'index']);
                Route::post('/', [MinecraftBuildController::class, 'store']);
                Route::get('name', [MinecraftBuildNameController::class, 'index']);

                Route::prefix('{build}')->group(function () {
                    Route::get('/', [MinecraftBuildController::class, 'show']);
                    Route::put('/', [MinecraftBuildController::class, 'update']);
                    Route::delete('/', [MinecraftBuildController::class, 'destroy']);
                    Route::patch('set', [MinecraftBuildController::class, 'patch']);

                    Route::post('vote', [MinecraftBuildVoteController::class, 'store']);
                    Route::delete('vote', [MinecraftBuildVoteController::class, 'destroy']);
                });
            });

            Route::prefix('telemetry')->group(function () {
                Route::post('seen', [MinecraftTelemetryController::class, 'playerSeen']);
            });

            Route::prefix('player/{minecraft_uuid}')->group(function () {
                Route::get('/', MinecraftPlayerControllerV2::class);
                Route::get('bans', [MinecraftPlayerBanController::class, 'index']);

                Route::prefix('register')->group(function () {
                    Route::post('/', [MinecraftRegisterController::class, 'store'])
                        ->middleware('throttle:3,1');

                    Route::put('/', [MinecraftRegisterController::class, 'update'])
                        ->middleware('throttle:12,1');
                });

                Route::prefix('home')->group(function () {
                    Route::get('/', [MinecraftPlayerHomeController::class, 'index']);
                    Route::post('/', [MinecraftPlayerHomeController::class, 'store']);
                    Route::get('name', [MinecraftPlayerHomeNameController::class, 'index']);
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

Route::domain(config('app.api_url'))
    ->prefix('v3')
    ->name('v3.')
    ->middleware('require-server-token')
    ->group(function () {
        Route::prefix('server')->group(function () {
            Route::post('connection/authorize', MinecraftConnectionAuthController::class);
            Route::post('connection/end', MinecraftConnectionEndController::class);
            Route::get('config', MinecraftConfigController::class);
        });

        Route::prefix('bans')->group(function () {
            Route::prefix('uuid')->group(function () {
                Route::post('/', [GamePlayerBanController::class, 'store']);
                Route::put('{banId}', [GamePlayerBanController::class, 'update']);
                Route::delete('{banId}', [GamePlayerBanController::class, 'delete']);
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
