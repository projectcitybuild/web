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
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IQRCodeProvider::class, function ($app) {
            return new EndroidQrCodeProvider();
        });
        $this->app->bind(TwoFactorAuth::class, function ($app) {
            return new TwoFactorAuth(
                issuer: "Project City Build",
                algorithm: Algorithm::Sha512,
                qrcodeprovider: $app->make(IQRCodeProvider::class),
            );
        });
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

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            // TODO: replace this with SPA url later
            return config('app.url') . "/api/new-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });

        $this->registerRateLimiters();
    }

    private function registerRateLimiters()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)
                ->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->getKey());
        });

        RateLimiter::for('password-confirm', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()->getKey());
        });
    }
}
