<?php

namespace App\Core\Support\Telescope;

use App\Core\Domains\Environment\EnvironmentLevel;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as LaravelTelescopeServiceProvider;

final class TelescopeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $isLocalEnv = $this->app->environment(EnvironmentLevel::DEVELOPMENT->value);

        // Not installed on production environments
        if ($isLocalEnv && class_exists(LaravelTelescopeServiceProvider::class)) {
            $this->app->register(LaravelTelescopeServiceProvider::class);
            $this->app->register(InternalTelescopeServiceProvider::class);
        }
    }
}
