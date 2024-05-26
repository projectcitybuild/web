<?php

namespace App;

use App\Models\Account;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRateLimiters();

        Cashier::useCustomerModel(Account::class);
    }

    private function registerRateLimiters()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)
                ->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->getKey());
        });

        RateLimiter::for('password-confirm', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->getKey());
        });
    }
}
