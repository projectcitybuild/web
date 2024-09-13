<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Prevents access unless the user has 2FA set-up and enabled on their account.
 */
class RequireMfaEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->is_totp_enabled) {
            return $next($request);
        }

        return redirect()
            ->route('front.account.settings.mfa')
            ->with('mfa_setup_required', true)
            ->with('mfa_setup_required_feature', 'the staff panel');
    }
}
