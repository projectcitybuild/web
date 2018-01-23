<?php
namespace App\Modules\ServerKeys\Services;

use App\Modules\ServerKeys\Repositories\ServerKeyTokenRepository;
use App\Modules\ServerKeys\Exceptions\InvalidTokenException;

class ServerKeyTokenAuthService {

    /**
     * @var ServerKeyTokenRepository
     */
    private $serverKeyTokenRepository;

    public function __construct(ServerKeyTokenRepository $serverKeyTokenRepository) {
        $this->serverKeyRepository = $serverKeyTokenRepository;
    }

    /**
     * Takes in an authorization request header and extracts
     * the bearer token from it.
     *
     * @param string $authHeader    String in the format of 'Bearer <token>'
     * 
     * @throws InvalidTokenException
     * @return string
     */
    public function getAuthHeader($authHeader) : string {
        if(empty($authHeader)) {
            throw new InvalidTokenException('No server token provided', InvalidTokenException::MALFORMED_TOKEN);
        }

        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            throw new InvalidTokenException('Malformed token. Requires a bearer', InvalidTokenException::MALFORMED_TOKEN);
        }

        return $matches[1];
    }

    /**
     * Takes in a server key's token and returns a ServerKey if
     * the token has not been blacklisted.
     *
     * @param string $token     ServerKeyToken string
     * 
     * @throws InvalidTokenException
     * @return ServerKey
     */
    public function getServerKey(string $token) : ServerKey {
        $serverToken = $this->serverKeyRepository->getByToken($token);

        if(!$serverToken) {
            throw new InvalidTokenException('Unauthorised token provided', InvalidTokenException::MALFORMED_TOKEN);
        }
        if($serverToken->is_blacklisted) {
            throw new InvalidTokenException('Provided token has expired', InvalidTokenException::EXPIRED_TOKEN);
        }
        if(is_null($serverToken->serverKey)) {
            throw new InvalidTokenException('Token does not belong to a server key', InvalidTokenException::NO_SERVER_KEY);
        }

        return $serverToken->serverKey;
    }

}