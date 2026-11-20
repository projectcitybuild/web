<?php

namespace App\Core\Support\Passport;

use App\Http\Controllers\Api\OAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

final class PassportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // For legacy compatibility with non-UUID client IDs (BookStack)
        Passport::$clientUuids = false;

        if (! $this->app->routesAreCached()) {
            Route::get('oauth/me', [OAuthController::class, 'show'])
                ->middleware('auth:api');
        }

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule::command('passport:purge')
                ->hourly();
        });

        Passport::tokensExpireIn(now()->addMinutes(30));
        Passport::refreshTokensExpireIn(now()->addDays(21));
        Passport::personalAccessTokensExpireIn(now()->addMonth());
        Passport::tokensCan([
            'openid' => 'Authenticate via OpenID Connect',
            'profile' => 'Access account profile information',
            'email' => 'Access the account email address',
            'discord.roles.sync' => 'Synchronize Discord roles',
        ]);
        Passport::setDefaultScope(['openid']);
    }
}
