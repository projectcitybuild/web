<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MfaGate
{
    /**
     * Routes to not redirect to the MFA gate on
     *
     * @var array|string[]
     */
    private array $exclude = [
        'front.login.mfa'
    ];
    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * MfaGate constructor.
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->accountDoesntNeedMfa($request)) {
            return $next($request);
        }

        return $this->responseFactory->redirectGuest(route('front.login.mfa'));
    }

    /**
     * Check if the given request does not require MFA first
     *
     * @param Request $request
     * @return bool
     */
    private function accountDoesntNeedMfa(Request  $request): bool
    {
        return $request->routeIs($this->exclude) ||
        $request->user() == null ||
        !$request->user()->is_totp_enabled ||
        !Session::has('auth.needs_mfa');
    }
}
