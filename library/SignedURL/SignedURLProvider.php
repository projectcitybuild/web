<?php

namespace Library\SignedURL;

use Illuminate\Support\ServiceProvider;
use Library\SignedURLs\Adapters\LaravelSignedURLGenerator;
use Library\SignedURLs\SignedURLGenerator;

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
