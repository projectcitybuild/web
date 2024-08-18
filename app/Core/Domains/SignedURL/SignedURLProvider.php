<?php

namespace App\Core\Domains\SignedURL;

use App\Core\Domains\SignedURL\Adapters\LaravelSignedURLGenerator;
use Illuminate\Support\ServiceProvider;

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
