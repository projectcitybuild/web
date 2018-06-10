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

Route::get('admin/emails', 'TempEmailAdminController@showView')->name('temp-email');
Route::post('admin/emails/sync', 'TempEmailAdminController@editEmail')->name('temp-email-save');

Route::redirect('terms',    'https://forums.projectcitybuild.com/t/terms-of-services/14506')->name('terms');
Route::redirect('privacy',  'https://forums.projectcitybuild.com/privacy')->name('privacy');

Route::get('/', [
    'as' => 'front.home',
    'uses' => 'HomeController@getView',
]);

Route::get('donations', [
    'as'    => 'front.donation-list',
    'uses'  => 'DonationController@getView',
]);

Route::group(['prefix' => 'account'], function() {
    Route::get('games', [
        'as'    => 'front.account.games',
        'uses'  => 'GameAccountController@showView',
    ]);
    Route::post('games', [
        'as'    => 'front.account.games.save',
        'uses'  => 'GameAccountController@saveAccounts',
    ]);
});

Route::group(['prefix' => 'login'], function() {
    Route::get('/', [
        'as'    => 'front.login',
        'uses'  => 'LoginController@showLoginView',
    ]);
    Route::post('/', [
        'as'    => 'front.login.submit',
        'uses'  => 'LoginController@login',
    ]);
    Route::get('google', [
        'as'    => 'front.login.google',
        'uses'  => 'LoginController@redirectToGoogle',
    ]);
    Route::get('google/callback', [
        'uses'  => 'LoginController@handleGoogleCallback',
    ]);
    Route::get('facebook', [
        'as'    => 'front.login.facebook',
        'uses'  => 'LoginController@redirectToFacebook',
    ]);
    Route::get('facebook/callback', [
        'uses'  => 'LoginController@handleFacebookCallback',
    ]);
    Route::get('twitter', [
        'as'    => 'front.login.twitter',
        'uses'  => 'LoginController@redirectToTwitter',
    ]);
    Route::get('twitter/callback', [
        'uses'  => 'LoginController@handleTwitterCallback',
    ]);
    Route::get('social/register', [
        'as'    => 'front.login.social-register',
        'uses'  => 'LoginController@createSocialAccount',
    ])->middleware('signed');
});

Route::group(['prefix' => 'password-reset'], function() {
    Route::get('/', [
        'as'    => 'front.password-reset',
        'uses'  => 'PasswordRecoveryController@showEmailForm',
    ]);
    Route::post('/', [
        'as'    => 'front.password-reset.submit',
        'uses'  => 'PasswordRecoveryController@sendVerificationEmail',
    ]);
    Route::get('recovery', [
        'as'    => 'front.password-reset.recovery',
        'uses'  => 'PasswordRecoveryController@showResetForm',
    ])->middleware('signed');
    
    Route::post('recovery', [
        'as'    => 'front.password-reset.save',
        'uses'  => 'PasswordRecoveryController@resetPassword',
    ]);
});

Route::group(['prefix' => 'register'], function() {
    Route::get('/', [
        'as'    => 'front.register',
        'uses'  => 'RegisterController@showRegisterView',
    ]);
    Route::post('/', [
        'as'    => 'front.register.submit',
        'uses'  => 'RegisterController@register',
    ]);
    Route::get('activate', [
        'as'    => 'front.register.activate',
        'uses'  => 'RegisterController@activate',
    ])->middleware('signed');
});

Route::get('logout/discourse', [
    'as'    => 'front.logout.pcb',
    'uses'  => 'LoginController@logoutFromDiscourse',
]);
Route::get('logout', [
    'as'    => 'front.logout',
    'uses'  => 'LoginController@logout',
]);

Route::view('bans', 'banlist')->name('banlist');

Route::post('deploy', 'DeployController@deploy');
Route::get('deploy', 'DeployController@deploy');
