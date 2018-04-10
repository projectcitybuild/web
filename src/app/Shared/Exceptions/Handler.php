<?php

namespace App\Shared\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Shared\Exceptions\BaseHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Exceptions not to report in API routes
     *
     * @var array
     */
    private $dontReportApi = [
        UnauthorisedException::class,
        BadRequestException::class,
        ForbiddenException::class,
        NotFoundException::class,
        TooManyRequestsException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if(env('APP_ENV') === 'production') {
            // don't report blacklisted exceptions on api routes
            if($request->is('api/*')) {
                $this->dontReport = array_merge($this->dontReport, $this->dontReportApi);
            }

            if(app()->bound('sentry') && $this->shouldReport($exception)) {
                app('sentry')->captureException($exception);
            }
        }
    
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // output exceptions in our API as JSON
        if($request->is('api/*') && $exception instanceof BaseHttpException) {
            $reflection = new \ReflectionClass($exception);

            return response()->json([
                'error' => [
                    'id'        => $exception->getId(),
                    'title'     => $reflection->getShortName(),
                    'detail'    => $exception->getMessage(),
                    'status'    => $exception->getStatusCode(),
                ],
            ], $exception->getStatusCode());
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
