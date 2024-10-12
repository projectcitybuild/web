<?php

namespace App\Core\Domains\SecureTokens;

use App\Core\Domains\SecureTokens\Adapters\HashedSecureTokenGenerator;
use Illuminate\Support\ServiceProvider;

class TokensProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SecureTokenGenerator::class, HashedSecureTokenGenerator::class);
    }
}
