<?php

namespace Library\Tokens;

use Illuminate\Support\ServiceProvider;
use Library\Discourse\Authentication\DiscoursePayloadValidator;
use Library\Tokens\Adapters\HashedTokenGenerator;
use function config;

class TokensProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenGenerator::class, fn () => new HashedTokenGenerator());
    }
}
