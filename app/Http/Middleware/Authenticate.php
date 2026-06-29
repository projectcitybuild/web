<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Http\Request;

class Authenticate extends AuthenticateMiddleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('front.login');
        }
    }
}
