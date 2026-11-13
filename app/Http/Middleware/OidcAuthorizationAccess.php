<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class OidcAuthorizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->query('response_type') === 'code'
            && (! is_string($request->query('code_challenge'))
                || $request->query('code_challenge') === ''
                || $request->query('code_challenge_method') !== 'S256')) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'Authorization code requests require a PKCE S256 challenge.',
            ], 400);
        }

        $account = Auth::guard(config('passport.guard', 'web'))->user();

        if (! $account) {
            return $next($request);
        }

        if ($account->is_totp_enabled && config('auth.totp.bypass')) {
            return response()->json([
                'error' => 'temporarily_unavailable',
                'error_description' => 'OIDC is unavailable while MFA verification bypass is enabled.',
            ], 503);
        }

        if (! $account->activated) {
            $this->preserveAuthorizationRequest($request);

            return redirect()->route('front.activate');
        }

        if ($account->is_totp_enabled && $request->session()->has(MfaAuthenticated::NEEDS_MFA_KEY)) {
            if (! $request->isMethod('GET')) {
                abort(403);
            }

            $this->preserveAuthorizationRequest($request);

            return redirect()->route('front.login.mfa');
        }

        return $next($request);
    }

    private function preserveAuthorizationRequest(Request $request): void
    {
        $url = url($request->path());

        if ($request->getQueryString()) {
            $url .= '?'.$request->getQueryString();
        }

        $request->session()->put('url.intended', $url);
    }
}
