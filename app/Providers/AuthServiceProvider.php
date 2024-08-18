<?php

namespace App\Providers;

use App\Http\Controllers\API\v1\OAuthController;
use App\Models\BanAppeal;
use App\Policies\BanAppealPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Library\Google2FA\Google2FAFake;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(BanAppeal::class, BanAppealPolicy::class);

        if (! $this->app->routesAreCached()) {
            Route::get('oauth/me', [OAuthController::class, 'show'])
                ->middleware('auth:api');
        }
    }

    /**
     * Register any auth-related application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('auth.totp.bypass')) {
            $this->app->bind(abstract: Google2FA::class, concrete: Google2FAFake::class);
        }
    }
}
