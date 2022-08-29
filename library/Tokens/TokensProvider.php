<?php

namespace Library\Tokens;

use Illuminate\Support\ServiceProvider;
use Library\Tokens\Adapters\HashedTokenGenerator;

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
