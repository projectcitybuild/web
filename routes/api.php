<?php

use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildController;
use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildNameController;
use App\Http\Controllers\Api\v2\Minecraft\Build\MinecraftBuildVoteController;
use App\Http\Controllers\Api\v2\Minecraft\MinecraftConfigController;
use App\Http\Controllers\Api\v2\Minecraft\MinecraftTelemetryController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeLimitController;
use App\Http\Controllers\Api\v2\Minecraft\Player\Homes\MinecraftPlayerHomeNameController;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftPlayerController;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftPlayerNicknameController;
use App\Http\Controllers\Api\v2\Minecraft\Player\MinecraftRegisterController;
use App\Http\Controllers\Api\v2\Minecraft\Warps\MinecraftWarpController;
use App\Http\Controllers\Api\v2\Minecraft\Warps\MinecraftWarpNameController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')
    ->name('v2.')
    ->middleware('require-server-token')
    ->group(function() {
        Route::prefix('minecraft')->group(function () {
            Route::get('config', MinecraftConfigController::class);

            Route::get('warp/name', [MinecraftWarpNameController::class, 'index']);
            Route::get('warp/all', [MinecraftWarpController::class, 'bulk']);
            Route::resource('warp', MinecraftWarpController::class);

            Route::prefix('build')->group(function () {
                Route::get('name', [MinecraftBuildNameController::class, 'index']);

                Route::prefix('{build}')->group(function () {
                    Route::patch('set', [MinecraftBuildController::class, 'patch']);

                    Route::post('vote', [MinecraftBuildVoteController::class, 'store']);
                    Route::delete('vote', [MinecraftBuildVoteController::class, 'destroy']);
                });
            });
            Route::resource('build', MinecraftBuildController::class);

            Route::prefix('telemetry')->group(function () {
                Route::post('seen', [MinecraftTelemetryController::class, 'playerSeen']);
            });

            Route::prefix('player/{minecraft_uuid}')->group(function () {
                Route::get('/', MinecraftPlayerController::class);
                Route::put('nickname', [MinecraftPlayerNicknameController::class, 'update']);

                Route::prefix('register')->group(function () {
                    Route::post('/', [MinecraftRegisterController::class, 'store'])
                        ->middleware('throttle:3,1');

                    Route::put('/', [MinecraftRegisterController::class, 'update'])
                        ->middleware('throttle:12,1');
                });

                Route::get('home/name', [MinecraftPlayerHomeNameController::class, 'index']);
                Route::get('home/limit', MinecraftPlayerHomeLimitController::class);
                Route::resource('home', MinecraftPlayerHomeController::class);
            });
        });
    });
