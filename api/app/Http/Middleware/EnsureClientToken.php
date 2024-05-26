<?php

namespace App\Http\Middleware;

use App\Models\ClientToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientToken
{
    private const pattern = '/^Bearer (.*)/i';

    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $token = $this->getBearerToken(
            $request->header('Authorization')
        );
        if ($token === null) {
            abort(code: 401);
        }

        $clientToken = ClientToken::where('token', $token)->first();

        if ($clientToken === null) {
            abort(code: 401);
        }
        $permitted = in_array(
            needle: $scope,
            haystack: $clientToken->permittedScopes(),
        );
        if (! $permitted) {
            abort(code: 403);
        }
        return $next($request);
    }

    private function getBearerToken(?string $header): ?string
    {
        if (empty($header)) {
            return null;
        }
        if (! preg_match(self::pattern, $header, $matches)) {
            return null;
        }
        if (count($matches) < 2 || empty($matches[1])) {
            return null;
        }
        return $matches[1];
    }
}
