<?php

namespace App\Services\PlayerBans;

use App\Exceptions\Http\ForbiddenException;
use App\Services\PlayerBans\Exceptions\MalformedTokenException;
use App\Services\PlayerBans\Exceptions\UnauthorisedTokenException;
use Entities\Models\Eloquent\ServerKey;
use Repositories\ServerKeyRepository;

/**
 * @deprecated Use Laravel Sanctum to check API token scopes
 */
final class ServerKeyAuthService
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
     * Takes in a server key's token and returns a ServerKey if
     * the token has not been blacklisted.
     *
     *
     * @throws InvalidTokenException
     */
    public function getServerKey(?string $authHeader): ServerKey
    {
        $token = $this->getTokenBearer($authHeader);
        $serverKey = $this->serverKeyRepository->getByToken($token);

        if ($serverKey === null) {
            throw new UnauthorisedTokenException('unauthorised_token', 'Unauthorised token provided');
        }

        return $serverKey;
    }

    /**
     * Extracts a bearer token from an authorization request header.
     *
     * @param  string  $authHeader  String in the format of 'Bearer <token>'
     *
     * @throws MalformedTokenException
     */
    private function getTokenBearer(?string $authHeader): string
    {
        if (empty($authHeader)) {
            throw new ForbiddenException('missing_token', 'No server token provided');
        }

        $matches = [];
        if (! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new MalformedTokenException('malformed_token', 'Malformed token. Requires a bearer');
        }

        return $matches[1];
    }
}
