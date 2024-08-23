<?php

namespace App\Core\Support\Laravel;

use App\Core\Support\Laravel\SignedURL\Adapters\LaravelSignedURLGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

final class LaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->app->bind(
            abstract: SignedURLGenerator::class,
            concrete: LaravelSignedURLGenerator::class,
        );

        // Fix the factory() function always searching for factory files with a relative namespace
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
