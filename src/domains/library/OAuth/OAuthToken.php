<?php

namespace Domains\Library\OAuth;

final class OAuthToken
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * @var int
     */
    private $expiry;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $scope;


    public function __construct(
        string $accessToken,
        string $tokenType,
        int $expiry,
        string $refreshToken,
        string $scope
    ) {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiry = $expiry;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
    }

    public function getAccessToken() : string
    {
        return $this->accessToken;
    }

    public function getTokenType() : string
    {
        return $this->tokenType;
    }

    public function getExpiry() : int
    {
        return $this->expiry;
    }

    public function getRefreshToken() : string
    {
        return $this->refreshToken;
    }

    public function getScope() : string
    {
        return $this->scope;
    }
}
