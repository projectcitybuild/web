<?php

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Data\Exceptions\BaseHttpException;
use App\Core\Data\Exceptions\ForbiddenException;
use App\Core\Data\Exceptions\NotFoundException;
use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Data\Exceptions\UnauthorisedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/web_panel.php',
            __DIR__.'/../routes/web_redirects.php',
        ],
        api: [
            __DIR__.'/../routes/api_v1.php',
            __DIR__.'/../routes/api_v2.php'
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            AuthenticationException::class,
            AuthorizationException::class,
            HttpException::class,
            ModelNotFoundException::class,
            TokenMismatchException::class,
            ValidationException::class,
            UnauthorisedException::class,
            BadRequestException::class,
            ForbiddenException::class,
            NotFoundException::class,
            TooManyRequestsException::class,
        ]);
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
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
