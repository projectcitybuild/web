<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Prevents access unless the account has been activated
 */
class RequireActivation
{
    public function handle(Request $request, Closure $next)
    {
        $account = $request->user();

        if ($account->activated) {
            return $next($request);
        }
        return redirect()->route('front.activate', [
            'email' => $account->email,
        ]);
    }
}
