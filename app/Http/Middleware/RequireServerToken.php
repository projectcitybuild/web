<?php

namespace App\Http\Middleware;

use App\Models\ServerToken;
use Closure;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\CauserResolver;

/**
 * Prevents access unless a valid ServerToken is present in the request
 * via a Bearer Authorization header
 */
class RequireServerToken
{
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');
        abort_if($authorization === null, code: 401);

        $hasAuthHeader = preg_match(
            pattern: '/Bearer\s(\S+)/',
            subject: $authorization,
            matches: $matches,
        );
        abort_if(! $hasAuthHeader, code: 401);

        $token = $matches[1];
        $serverToken = ServerToken::where('token', $token)->first();
        abort_if($serverToken === null, code: 401);

        CauserResolver::setCauser($serverToken->server);

        return $next($request);
    }
}
