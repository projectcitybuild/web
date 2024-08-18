<?php

namespace App\Http\Middleware;

use App\Domains\ServerTokens\ScopeKey;
use App\Models\ServerToken;
use Closure;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\CauserResolver;

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
            abort(401);
        }
        if (! preg_match(pattern: '/Bearer\s(\S+)/', subject: $authorization, matches: $matches)) {
            abort(401);
        }
        $rawToken = $matches[1];

        $token = ServerToken::where('token', $rawToken)
            ->with('scopes')
            ->first();

        if ($token === null) {
            abort(401);
        }

        $hasScope = $token->scopes->contains(fn ($s) => $s->scope === $scope);
        if (! $hasScope) {
            abort(403);
        }

        $request->token = $token;

        CauserResolver::setCauser($token->server);

        return $next($request);
    }

    public static function middleware(ScopeKey $scopeKey): string
    {
        return 'server-token:'.$scopeKey->value;
    }
}
