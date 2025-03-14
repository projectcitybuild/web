<?php

namespace App\Core\Support\Passport;

use App\Http\Controllers\Api\OAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

final class PassportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            Route::get('oauth/me', [OAuthController::class, 'show'])
                ->middleware('auth:api');
        }

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule::command('passport:purge')
                ->hourly();
        });
    }
}
