<?php

namespace App\core\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DenyIFrameMiddleware {

    /**
     * Adds a header to an outgoing response to prevent
     * being loaded in an iframe
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        $response = $next($request);
        $response->header('X-Frame-Options', 'DENY');
        return $response;
    }
}
