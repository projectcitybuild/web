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
        $token = $request->headers->get('Authorization');
        if(empty($token) || is_null($token)) {
            abort(401, 'No server token provided');
        }

        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            abort(400, 'Malformed token. Requires a bearer');
        }

        $serverKey = $this->tokenAuthService->getServerKey($matches[1]);

        // pass the server key down to the controller
        $request->attributes->add(['key' => $serverKey]);

        return $next($request);
    }
}