<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PanelAccess
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if (! Auth::check() || ! $request->user()->canAccessPanel()) {
            abort(403);
        }

        return $next($request);
    }
}
