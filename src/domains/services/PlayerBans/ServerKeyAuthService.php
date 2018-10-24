<?php
namespace Domains\Services\PlayerBans;

use Entities\ServerKeys\Repositories\ServerKeyRepository;
use Entities\ServerKeys\Models\ServerKey;
use Domains\Services\PlayerBans\Exceptions\MalformedTokenException;
use Domains\Services\PlayerBans\Exceptions\UnauthorisedTokenException;
use Application\Exceptions\ForbiddenException;

class ServerKeyAuthService
{
    /**
     * @var ServerKeyRepository
     */
    private $serverKeyRepository;
    

    public function __construct(ServerKeyRepository $serverKeyRepository)
    {
        $this->serverKeyRepository = $serverKeyRepository;
    }

    /**
     * Extracts a bearer token from an authorization request header
     *
     * @param string $authHeader    String in the format of 'Bearer <token>'
     *
     * @throws MalformedTokenException
     * @return string
     */
    private function getTokenBearer(?string $authHeader) : string
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
    public function getServerKey(?string $authHeader) : ServerKey
    {
        $token = $this->getTokenBearer($authHeader);
        $serverKey = $this->serverKeyRepository->getByToken($token);

        if ($serverKey === null) {
            throw new UnauthorisedTokenException('unauthorised_token', 'Unauthorised token provided');
        }

        return $serverKey;
    }
}
