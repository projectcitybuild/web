<?php

namespace App\Exceptions;

use App\Exceptions\Http\BadRequestException;
use App\Exceptions\Http\BaseHttpException;
use App\Exceptions\Http\ForbiddenException;
use App\Exceptions\Http\NotFoundException;
use App\Exceptions\Http\TooManyRequestsException;
use App\Exceptions\Http\UnauthorisedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
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
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        });

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Throwable  $e
     * @return Response|JsonResponse|RedirectResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse
    {
        // Convert all exceptions to a consistent JSON format
        if ($request->is(patterns: 'api/*') && $e instanceof BaseHttpException) {
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

        return parent::render($request, $e);
    }
}
