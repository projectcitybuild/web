<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class DatabaseFactoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Can't auto-resolve model factories because our models aren't located in `App/Models`
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $modelName = Str::after($modelName, "Eloquent\\");
            return "Database\\Factories\\" . $modelName . "Factory";
        });
    }
}
