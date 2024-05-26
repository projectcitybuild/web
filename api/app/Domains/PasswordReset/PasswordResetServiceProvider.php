<?php

namespace App\Domains\PasswordReset;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class PasswordResetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            // TODO: replace this with SPA url later
            return config('app.url') . "/api/new-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
