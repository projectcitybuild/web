<?php

namespace Library\Random;

use Illuminate\Support\ServiceProvider;
use Library\Random\Adapters\RandomStringConcrete;

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
