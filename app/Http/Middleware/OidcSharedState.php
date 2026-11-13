<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OidcSharedState
{
    public function handle(Request $request, Closure $next): Response
    {
        $redis = config('database.redis.default', []);
        $cacheRedis = config('database.redis.cache', []);
        $sameRedisServer = collect(['url', 'host', 'username', 'password', 'port'])
            ->every(fn (string $key): bool => ($redis[$key] ?? null) === ($cacheRedis[$key] ?? null));

        if (config('session.driver') !== 'redis'
            || config('cache.default') !== 'redis'
            || config('session.connection') !== config('oidc-server.freshness.redis_connection')
            || ! $sameRedisServer
            || (app()->isProduction() && ! str_starts_with((string) config('oidc-server.issuer'), 'https://'))) {
            return response()->json([
                'error' => 'server_error',
                'error_description' => 'OIDC requires shared Redis-backed sessions, cache locks, and a canonical HTTPS issuer.',
            ], 503);
        }

        return $next($request);
    }
}
