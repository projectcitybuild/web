<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('bans')->group(function () {
    Route::post('list', 'GameBanController@getBanList');
    Route::post('store/ban', 'GameBanController@storeBan');
    Route::post('store/unban', 'GameBanController@storeUnban');
    Route::post('status', 'GameBanController@getPlayerStatus');
});

Route::prefix('auth')->group(function () {
    Route::post('minecraft/request-url', [
        'as'   => 'auth.minecraft.request_url',
        'uses' => 'MinecraftAuthenticationController@requestTokenUrl',
    ]);
    Route::get('minecraft/groups', [
        'as'   => 'auth.minecraft.groups',
        'uses' => 'MinecraftAuthenticationController@getGroupsForUUID',
    ]);
});

Route::prefix('groups')->group(function () {
    Route::get('/', 'GroupApiController@getAll');
});

Route::prefix('servers')->group(function () {
    Route::get('all', 'ServerController@getAllServers');
});

Route::post('discord/sync', 'DiscordSyncController@getRank');

/**
 * @deprecated 1.11.0
 */
Route::post('minecraft/authenticate', 'TempMinecraftController@authenticate');
