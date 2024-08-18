<?php

namespace App\Core\Domains\Random;

use App\Core\Domains\Random\Adapters\RandomStringConcrete;
use Illuminate\Support\ServiceProvider;

class RandomProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RandomStringGenerator::class, RandomStringConcrete::class);
    }
}
