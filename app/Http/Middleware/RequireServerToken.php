<?php

namespace App\Http\Middleware;

use App\Models\ServerToken;
use Closure;
use Illuminate\Http\Request;

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

        $serverToken = $this->serverTokenForAuthHeader($authorization);
        abort_if($serverToken === null, code: 401);

        $this->assertWhitelistedIp($request, $serverToken);

        return $next($request);
    }

    private function serverTokenForAuthHeader(string $authHeader): ?ServerToken
    {
        $hasAuthHeader = preg_match(
            pattern: '/Bearer\s(\S+)/',
            subject: $authHeader,
            matches: $matches,
        );
        abort_if(! $hasAuthHeader, code: 401, message: 'Invalid authorization header. Must be a bearer token');

        $token = $matches[1];
        return ServerToken::where('token', $token)->first();
    }

    private function assertWhitelistedIp(Request $request, ServerToken $serverToken): void
    {
        $ip = $request->ip();
        abort_if(empty($ip), code: 401, message: 'Could not determine IP address');

        if (empty($serverToken->allowed_ips)) {
            return;
        }
        $allowedIps = explode(',', $serverToken->allowed_ips);
        abort_if(! in_array($ip, $allowedIps), code: 403, message: 'IP address is not whitelisted');
    }
}
