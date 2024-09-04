<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveMfaSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has(MfaAuthenticated::NEEDS_MFA_KEY)) {
            return redirect(route('front.account.settings'));
        }

        return $next($request);
    }
}
