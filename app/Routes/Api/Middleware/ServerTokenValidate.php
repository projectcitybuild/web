<?php

namespace App\Routes\Api\Middleware;

use App\Modules\Servers\Repositories\{ServerKeyRepository, ServerKeyTokenRepository};
use Illuminate\Http\Request;
use Closure;

class ServerTokenValidate {

    private $keyTokenRepository;
    private $keyRepository;

    public function __construct(ServerKeyTokenRepository $keyTokenRepository, ServerKeyRepository $keyRepository) {
        $this->keyTokenRepository = $keyTokenRepository;
        $this->keyRepository = $keyRepository;
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
        if(empty($token)) {
            abort(401, 'No server token provided');
        }

        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            abort(400, 'Malformed token. Requires a bearer');
        }

        $serverToken = $this->keyTokenRepository->getByToken($matches[1]);
        if(!$serverToken) {
            abort(403, 'Unauthorised token provided');
        }
        if($serverToken->is_blacklisted) {
            abort(403, 'Provided token has expired');
        }

        $serverKey = $this->keyRepository->getById($serverToken->server_key_id);
        if(is_null($serverKey)) {
            abort(404, 'Server key does not exist');
        }

        // pass the token and key model down
        $request->attributes->add([
            'token' => $serverToken,
            'key'   => $serverKey,
        ]);

        return $next($request);
    }
}