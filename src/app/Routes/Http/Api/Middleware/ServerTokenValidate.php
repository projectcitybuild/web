<?php

namespace App\Routes\Api\Middleware;

use App\Modules\ServerKeys\Services\ServerKeyTokenAuthService;
use Illuminate\Http\Request;
use Closure;

class ServerTokenValidate {

    /**
     * @var ServerKeyTokenAuthService
     */
    private $tokenAuthService;

    public function __construct(ServerKeyTokenAuthService $tokenAuthService) {
        $this->tokenAuthService = $tokenAuthService;
    }

    /**
     * Validates that the request has a valid server token in the Authorization header
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $header = $request->headers->get('Authorization');

        $token = $this->tokenAuthService->getAuthHeader($header);
        $key = $this->tokenAuthService->getServerKey($token);

        // pass the server key down to the controller
        $request->attributes->add(['key' => $key]);

        return $next($request);
    }
}