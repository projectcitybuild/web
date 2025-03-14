<?php

namespace App\Core\Support\Telescope;

use App\Core\Domains\Environment\EnvironmentLevel;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as LaravelTelescopeServiceProvider;

final class TelescopeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->enabled()) return;

        $this->app->register(LaravelTelescopeServiceProvider::class);
        $this->app->register(InternalTelescopeServiceProvider::class);

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule::command('telescope:prune')
                ->daily();
        });
    }

    private function enabled(): bool
    {
        $isLocalEnv = $this->app->environment(EnvironmentLevel::DEVELOPMENT->value);

        // Not installed on production environments
        $isInstalled = class_exists(LaravelTelescopeServiceProvider::class);

        return $isLocalEnv && $isInstalled;
    }
}
