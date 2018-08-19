<?php

use Application\Environment;

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

// url redirects
Route::redirect('terms', 'https://forums.projectcitybuild.com/t/terms-of-services/14506')->name('terms');
Route::redirect('privacy', 'https://forums.projectcitybuild.com/privacy')->name('privacy');

// sentry confirmation route
Route::get('sentry/test', function () {
    throw new \Exception('Sentry test');
});

Route::get('/', [
    'as' => 'front.home',
    'uses' => 'HomeController@getView',
]);

Route::get('donate', [
    'as' => 'front.donate',
    'uses' => 'DonationController@getView',
]);
Route::post('donate/charge', [
    'as'    => 'front.donate.charge',
    'uses'  => 'DonationController@donate',
]);

Route::get('donations', [
    'as'    => 'front.donation-list',
    'uses'  => 'DonationController@getListView',
]);

Route::prefix('login')->group(function () {
    Route::get('/', [
        'as'    => 'front.login',
        'uses'  => 'LoginController@loginOrShowForm',
    ]);
    Route::post('/', [
        'as'    => 'front.login.submit',
        'uses'  => 'LoginController@login',
    ]);
    Route::get('{provider}/redirect', [
        'as'    => 'front.login.provider.redirect',
        'uses'  => 'LoginController@redirectToProvider',
    ]);
    Route::get('{provider}/callback', [
        'as'    => 'front.login.provider.callback',
        'uses'  => 'LoginController@handleProviderCallback',
    ]);
    Route::get('social/register', [
        'as'    => 'front.login.social-register',
        'uses'  => 'LoginController@createSocialAccount',
    ])
    ->middleware('signed');
});

Route::prefix('password-reset')->group(function () {
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

Route::prefix('register')->group(function () {
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


Route::group(['prefix' => 'account', 'middleware' => 'auth'], function () {
    Route::prefix('settings')->group(function () {
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

    Route::prefix('social')->group(function () {
        Route::get('/', [
            'as'    => 'front.account.social',
            'uses'  => 'AccountSocialController@showView',
        ]);

        Route::get('{provider}/redirect', [
            'as'    => 'front.account.social.redirect',
            'uses'  => 'AccountSocialController@redirectToProvider',
        ]);
        
        Route::get('{provider}/callback', [
            'as'    => 'front.account.social.callback',
            'uses'  => 'AccountSocialController@handleProviderCallback',
        ]);

        Route::get('{provider}/delete', [
            'as'    => 'front.account.social.delete',
            'uses'  => 'AccountSocialController@deleteLink',
        ]);
    });
   
    Route::prefix('games')->group(function () {
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


Route::view('bans', 'front.pages.banlist')->name('banlist');

Route::post('deploy', 'DeployController@deploy');
Route::get('deploy', 'DeployController@deploy');
