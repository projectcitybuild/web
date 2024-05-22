<?php

use App\Http\Middleware\EnsureClientToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            // Stripe webhooks need to bypass CSRF protection
            // https://laravel.com/docs/11.x/billing#webhooks-csrf-protection
           'stripe/*',
        ]);
        $middleware->alias([
           'client-token' => EnsureClientToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
