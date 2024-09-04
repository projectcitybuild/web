<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MfaAuthenticated
{
    const NEEDS_MFA_KEY = 'auth.needs-mfa';

    public function __construct(
        private readonly ResponseFactory $responseFactory,
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->accountDoesntNeedMfa($request)) {
            return $next($request);
        }
        return $this->responseFactory->redirectGuest(route('front.login.mfa'));
    }

    private function accountDoesntNeedMfa(Request $request): bool
    {
        return ! $request->user()->is_totp_enabled ||
            ! Session::has(self::NEEDS_MFA_KEY);
    }
}
