<?php

namespace App\Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Authentication\Services\LoginRateLimitService;
use Illuminate\Support\Facades\Session;
use App\Library\RateLimit\TokenBucket;
use App\Library\RateLimit\Storage\SessionTokenStorage;
use App\Library\RateLimit\Rate;

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
        $rate = Rate::refill(3)->every(3, Rate::SECONDS);

        $sessionStorage = new SessionTokenStorage('login.rate', 5);
        $rateLimiter = new TokenBucket(5, $rate, $sessionStorage);
        $rateLimiter->consume(2);

        $this->app->singleton(LoginRateLimitService::class, function($app) {
            $rateLimiter = new LoginRateLimitService();
            $rateLimiter->bootstrap();

            return $rateLimiter;
        });
    }
}
