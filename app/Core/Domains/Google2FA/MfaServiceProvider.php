<?php

namespace App\Core\Domains\Google2FA;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use PragmaRX\Google2FA\Google2FA;

class MfaServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (config('auth.totp.bypass')) {
            $this->app->bind(abstract: Google2FA::class, concrete: Google2FAFake::class);
        }
    }
}
