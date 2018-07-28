<?php
namespace Domains\Modules\ServerKeys\Services;

use Domains\Modules\ServerKeys\Repositories\ServerKeyTokenRepository;
use Domains\Modules\ServerKeys\Exceptions\MalformedTokenException;
use Domains\Modules\ServerKeys\Exceptions\ExpiredTokenException;
use Domains\Modules\ServerKeys\Exceptions\UnauthorisedTokenException;
use Domains\Modules\ServerKeys\Models\ServerKey;
use Application\Exceptions\ForbiddenException;

class ServerKeyTokenAuthService
{

    /**
     * @var ServerKeyTokenRepository
     */
    private $tokenRepository;

    public function __construct(ServerKeyTokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Takes in an authorization request header and extracts
     * the bearer token from it.
     *
     * @param string $authHeader    String in the format of 'Bearer <token>'
     *
     * @throws MalformedTokenException
     * @return string
     */
    public function getAuthHeader($authHeader) : string
    {
        if (empty($authHeader)) {
            throw new ForbiddenException('missing_token', 'No server token provided');
        }

        $matches = [];
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new MalformedTokenException('malformed_token', 'Malformed token. Requires a bearer');
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
    public function getServerKey(string $token) : ServerKey
    {
        $serverToken = $this->tokenRepository->getByToken($token);

        if (!$serverToken) {
            throw new UnauthorisedTokenException('unauthorised_token', 'Unauthorised token provided');
        }
        if ($serverToken->is_blacklisted) {
            throw new ExpiredTokenException('expired_token', 'Provided token has expired');
        }
        if (is_null($serverToken->serverKey)) {
            throw new UnauthorisedTokenException('no_key_token', 'Token does not have an assigned server key');
        }

        return $serverToken->serverKey;
    }
}
