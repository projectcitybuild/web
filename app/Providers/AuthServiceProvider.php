<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    /**
     * Register any auth-related application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('auth.totp.bypass')) {
            $this->app->bind(Google2FA::class, function ($app) {
                return new Google2FAFake();
            });
        }
    }
}
