<?php

namespace Library\SignedURL;

use Illuminate\Support\ServiceProvider;
use Library\SignedURL\Adapters\LaravelSignedURLGenerator;

class SignedURLProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(abstract: SignedURLGenerator::class, concrete: LaravelSignedURLGenerator::class);
    }
}
