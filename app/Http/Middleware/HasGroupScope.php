<?php

namespace App\Http\Middleware;

use Closure;

class HasGroupScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     */
    public function handle($request, Closure $next, $scope)
    {
        // Ordinarily we would use the in-built `can:scope_name` gate middleware,
        // but it seems the route type changes the exception automatically thrown.
        // For example, `Route::resource(...)` throws 401 and `Route::get(...)`
        // throws 403...

        if (! empty($scope)) {
            $account = $request->user();
            if ($account === null) {
                abort(403);
            }
            if (! $account->hasAbility(to: $scope)) {
                abort(401);
            }
        }

        return $next($request);
    }
}
