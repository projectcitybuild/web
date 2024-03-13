<?php

namespace App\Providers;

use App\Models\MinecraftUUID;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('minecraft_uuid', function ($value) {
            return new MinecraftUUID($value);
        });

        // Can't auto-resolve model factories because our models aren't located in `App/Models`
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $modelName = Str::after($modelName, "Eloquent\\");
            return "Database\\Factories\\" . $modelName . "Factory";
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
