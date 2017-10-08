<?php

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

Route::prefix('bans')->group(function() {
    Route::post('list',         'BanController@getBanList');
    
    Route::middleware('auth.token.server')->group(function() {
        Route::post('store/ban',    'BanController@storeBan');
        Route::post('store/unban',  'BanController@storeUnban');
        Route::post('status',       'BanController@checkUserStatus');
        Route::post('history',      'BanController@getUserBanHistory');
    });
});

Route::prefix('servers')->group(function() {
    Route::get('all', 'ServerController@getAllServers');
});

