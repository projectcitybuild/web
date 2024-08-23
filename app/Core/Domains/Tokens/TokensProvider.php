<?php

namespace App\Core\Domains\Tokens;

use App\Core\Domains\Tokens\Adapters\HashedTokenGenerator;
use Illuminate\Support\ServiceProvider;

class TokensProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenGenerator::class, HashedTokenGenerator::class);
    }
}
