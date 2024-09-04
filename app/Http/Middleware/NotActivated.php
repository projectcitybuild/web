<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Prevents access if the account has already been activated
 */
class NotActivated
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->activated) {
            return redirect()->route('front.account.profile');
        }
        return $next($request);
    }
}
