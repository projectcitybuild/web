<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ConfigurationUrlParser;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class OidcSharedState
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionConnection = config('session.connection');
        $cacheConnection = config('cache.stores.redis.connection');
        $redis = is_string($sessionConnection)
            ? config('database.redis.'.$sessionConnection, [])
            : [];
        $cacheRedis = is_string($cacheConnection)
            ? config('database.redis.'.$cacheConnection, [])
            : [];
        $sameRedisServer = $this->sameRedisServer($redis, $cacheRedis);
        $redisDatabasesConfigured = collect([$redis, $cacheRedis])->every(fn (array $connection): bool => is_int($connection['database'] ?? null)
                ? $connection['database'] >= 0
                : (is_string($connection['database'] ?? null) && ctype_digit($connection['database']))
        );

        if (config('session.driver') !== 'redis'
            || config('cache.default') !== 'redis'
            || ! is_string($sessionConnection)
            || $sessionConnection === ''
            || $sessionConnection !== config('oidc-server.freshness.redis_connection')
            || ! is_string($cacheConnection)
            || $cacheConnection === ''
            || ! $sameRedisServer
            || ! $redisDatabasesConfigured
            || (app()->isProduction() && ! str_starts_with((string) config('oidc-server.issuer'), 'https://'))) {
            return response()->json([
                'error' => 'server_error',
                'error_description' => 'OIDC requires shared Redis-backed sessions, cache locks, and a canonical HTTPS issuer.',
            ], 503);
        }

        return $next($request);
    }

    private function sameRedisServer(array $redis, array $cacheRedis): bool
    {
        try {
            $parser = new ConfigurationUrlParser;
            $redis = $parser->parseConfiguration($redis);
            $cacheRedis = $parser->parseConfiguration($cacheRedis);
        } catch (InvalidArgumentException) {
            return false;
        }

        $redisScheme = $redis['scheme'] ?? $redis['driver'] ?? 'tcp';
        $cacheScheme = $cacheRedis['scheme'] ?? $cacheRedis['driver'] ?? 'tcp';

        return $redisScheme === $cacheScheme
            && strtolower((string) ($redis['host'] ?? '')) === strtolower((string) ($cacheRedis['host'] ?? ''))
            && (int) ($redis['port'] ?? 6379) === (int) ($cacheRedis['port'] ?? 6379)
            && ($redis['username'] ?? null) === ($cacheRedis['username'] ?? null)
            && ($redis['password'] ?? null) === ($cacheRedis['password'] ?? null)
            && ($redis['context'] ?? null) === ($cacheRedis['context'] ?? null)
            && ($redis['ssl'] ?? null) === ($cacheRedis['ssl'] ?? null)
            && ($redis['tls'] ?? null) === ($cacheRedis['tls'] ?? null)
            && ($redis['path'] ?? null) === ($cacheRedis['path'] ?? null);
    }
}
