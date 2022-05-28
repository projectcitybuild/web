<?php

namespace App\Http\Middleware;

use Closure;
use Entities\Models\Eloquent\ServerToken;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RequiresServerTokenScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $scope)
    {
        $authorization = $request->header('Authorization');
        if ($authorization === null) {
            abort(403);
        }
        if (! preg_match(pattern: '/Bearer\s(\S+)/', subject: $authorization, matches: $matches)) {
            abort(403);
        }
        $rawToken = $matches[1];

        $token = ServerToken::where('token', $rawToken)
            ->with('scopes')
            ->first();

        if ($token === null) {
            abort(403);
        }

        $hasScope = $token->scopes->contains(fn ($s) => $s->scope === $scope);
        if (! $hasScope) {
            abort(403);
        }

        return $next($request);
    }
}
