<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Just forwards to Laravel's `RequirePassword`, but with a default
 * redirect route injected. Otherwise we would need to specify the
 * redirect route manually on every route that uses the middleware
 */
class RequirePassword extends \Illuminate\Auth\Middleware\RequirePassword
{
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null)
    {
        return parent::handle($request, $next, 'front.password.confirm');
    }
}
