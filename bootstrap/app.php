<?php

use App\Core\Data\Exceptions\BaseHttpException;
use App\Http\Middleware\ActiveMfaSession;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LogApiCalls;
use App\Http\Middleware\MfaAuthenticated;
use App\Http\Middleware\NotActivated;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RequireActivation;
use App\Http\Middleware\RequireMfaEnabled;
use App\Http\Middleware\RequirePassword;
use App\Http\Middleware\RequireServerToken;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;
use Stripe\Exception\OAuth\UnsupportedGrantTypeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/web_manage.php',
            __DIR__.'/../routes/web_review.php',
            __DIR__.'/../routes/web_redirects.php',
            __DIR__.'/../routes/web_tests.php',
        ],
        api: [
            __DIR__.'/../routes/api.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '', // Removes /api prefix
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->replace(
            search: TrustProxies::class,
            replace: App\Http\Middleware\TrustProxies::class,
        );
        $middleware->web(append: [
            VerifyCsrfToken::class,
            HandleInertiaRequests::class,
        ]);
        $middleware->api(append: [
            LogApiCalls::class,
        ]);
        $middleware->redirectGuestsTo(
            fn (Request $request) => route('front.login'),
        );
        $middleware->alias([
            'active-mfa' => ActiveMfaSession::class,
            'activated' => RequireActivation::class,
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'mfa' => MfaAuthenticated::class,
            'not-activated' => NotActivated::class,
            'password.confirm' => RequirePassword::class,
            'require-mfa' => RequireMfaEnabled::class,
            'require-server-token' => RequireServerToken::class,
        ]);
        // Stripe webhooks need to bypass Laravel's CSRF protection
        // https://laravel.com/docs/12.x/billing#handling-stripe-webhooks
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            AuthenticationException::class,
            AuthorizationException::class,
            HttpException::class,
            ModelNotFoundException::class,
            TokenMismatchException::class,
            ValidationException::class,
            UnsupportedGrantTypeException::class,
        ]);
        $exceptions->render(function (BaseHttpException $e, Request $request) {
            // Convert all exceptions to a consistent JSON format
            if ($request->is(patterns: 'api/*')) {
                return response()->json(
                    data: [
                        'error' => [
                            'id' => $e->getId(),
                            'title' => '',  /** @deprecated */
                            'detail' => $e->getMessage(),
                            'status' => $e->getStatusCode(),
                        ],
                    ],
                    status: $e->getStatusCode(),
                );
            }
        });
        Integration::handles($exceptions);
    })->create();
