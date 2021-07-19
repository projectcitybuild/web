<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * This middleware prevents the user from accessing a route unless they have 2FA enabled on their account.
 */
class RequiresMfaEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->is_totp_enabled) {
            return $next($request);
        }

        return redirect(route('front.account.security'))
            ->with('mfa_setup_required', true)
            ->with('mfa_setup_required_feature', 'the staff panel');
    }
}
