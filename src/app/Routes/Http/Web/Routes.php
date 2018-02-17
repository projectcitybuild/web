<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// force all web routes to load from https
if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}


Route::get('/', [
    'uses' => 'HomeController@getView',
    'as' => 'home',
]);

Route::view('bans', 'banlist')->name('banlist');

Route::post('deploy', 'DeployController@deploy');
Route::get('deploy', 'DeployController@deploy');