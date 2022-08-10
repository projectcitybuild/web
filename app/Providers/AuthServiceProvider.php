<?php

namespace App\Providers;

use App\Http\Controllers\Api\v1\OAuthController;
use App\Policies\BanAppealPolicy;
use Entities\Models\Eloquent\BanAppeal;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use Library\Google2FA\Google2FAFake;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        BanAppeal::class => BanAppealPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();

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
