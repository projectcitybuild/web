<?php

use App\Core\Environment;

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

// force all web routes to load over https
if (Environment::isProduction()) {
    URL::forceScheme('https');
}

Route::redirect('terms',            'https://forums.projectcitybuild.com/t/terms-of-services/14506')->name('terms');
Route::redirect('privacy',          'https://forums.projectcitybuild.com/privacy')->name('privacy');
Route::redirect('discourse/login',  'https://forums.projectcitybuild.com/login')->name('login.redirect');

Route::get('sentry/test', function() {
   throw new \Exception('Sentry test');
});

Route::get('/', [
    'as' => 'front.home',
    'uses' => 'HomeController@getView',
]);

Route::get('donations', [
    'as'    => 'front.donation-list',
    'uses'  => 'DonationController@getView',
]);

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
    ])
    ->middleware('signed');
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
    ])
    ->middleware('signed');
    
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


Route::group(['prefix' => 'account', 'middleware' => 'auth'], function() {

    Route::group(['prefix' => 'settings'], function() {
        Route::get('/', [
            'as'    => 'front.account.settings',
            'uses'  => 'AccountSettingController@showView',
        ]);

        Route::post('email/verify', [
            'as'    => 'front.account.settings.email',
            'uses'  => 'AccountSettingController@sendVerificationEmail',
        ]);

        Route::get('email/confirm', [
            'as'    => 'front.account.settings.email.confirm',
            'uses'  => 'AccountSettingController@showConfirmForm',
        ])
        ->middleware('signed');
    
        Route::post('email/confirm', [
            'as'    => 'front.account.settings.email.confirm.save',
            'uses'  => 'AccountSettingController@confirmEmailChange',
        ]);

        Route::post('password', [
            'as'    => 'front.account.settings.password',
            'uses'  => 'AccountSettingController@changePassword',
        ]);
    });
   
    Route::group(['prefix' => 'games'], function() {
        Route::get('games', [
            'as'    => 'front.account.games',
            'uses'  => 'GameAccountController@showView',
        ]);

        Route::post('games', [
            'as'    => 'front.account.games.save',
            'uses'  => 'GameAccountController@saveAccounts',
        ]);
    });
});

Route::view('bans', 'banlist')->name('banlist');

Route::post('deploy', 'DeployController@deploy');
Route::get('deploy', 'DeployController@deploy');
