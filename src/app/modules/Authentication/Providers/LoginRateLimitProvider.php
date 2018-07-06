<?php

namespace App\Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Authentication\Services\LoginRateLimitService;
use Illuminate\Support\Facades\Session;

class LoginRateLimitProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(LoginRateLimitService::class, function($app) {
            $rateLimiter = new LoginRateLimitService();
            $rateLimiter->bootstrap();

            return $rateLimiter;
        });
    }
}
