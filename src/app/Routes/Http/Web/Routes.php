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
    'as' => 'front.home',
    'uses' => 'HomeController@getView',
]);
Route::get('login', [
    'as'    => 'front.login',
    'uses'  => 'LoginController@showLoginView',
]);
Route::post('login', [
    'as'    => 'front.login.submit',
    'uses'  => 'LoginController@login',
]);
Route::get('logout/discourse', [
    'as'    => 'front.logout.pcb',
    'uses'  => 'LoginController@logoutFromDiscourse',
]);
Route::get('logout', [
    'as'    => 'front.logout',
    'uses'  => 'LoginController@logout',
]);
Route::get('register', [
    'as'    => 'front.register',
    'uses'  => 'RegisterController@showRegisterView',
]);
Route::post('register', [
    'as'    => 'front.register.submit',
    'uses'  => 'RegisterController@register',
]);
Route::get('register/activate', [
    'as'    => 'front.register.activate',
    'uses'  => 'RegisterController@activate',
]);

Route::view('bans', 'banlist')->name('banlist');

Route::post('deploy', 'DeployController@deploy');
Route::get('deploy', 'DeployController@deploy');
