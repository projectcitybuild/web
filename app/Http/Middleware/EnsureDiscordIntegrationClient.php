<?php

namespace App\Http\Middleware;

use Laravel\Passport\AccessToken;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Contracts\ScopeAuthorizable;
use Laravel\Passport\Exceptions\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\ValidateToken;
use Laravel\Passport\Passport;

final class EnsureDiscordIntegrationClient extends ValidateToken
{
    /**
     * Ensure only the configured machine client can access Discord integration routes.
     *
     * @throws AuthenticationException
     * @throws MissingScopeException
     */
    protected function validate(ScopeAuthorizable $token, string ...$params): void
    {
        $clientId = config('services.discord.passport_client_id');

        if (
            ! $token instanceof AccessToken
            || ! is_string($clientId)
            || $clientId === ''
            || (string) $token->oauth_client_id !== $clientId
            || (string) $token->oauth_user_id !== $clientId
        ) {
            throw new AuthenticationException;
        }

        $client = app(ClientRepository::class)->findActive($clientId);
        $accessTokenId = $token->oauth_access_token_id ?? null;

        if (
            $client === null
            || ! $client->hasGrantType('client_credentials')
            || ! is_string($accessTokenId)
            || ! Passport::token()->newQuery()
                ->whereKey($accessTokenId)
                ->where('client_id', $clientId)
                ->whereNull('user_id')
                ->exists()
        ) {
            throw new AuthenticationException;
        }

        foreach ($params as $scope) {
            if ($token->cant($scope)) {
                throw new MissingScopeException($scope);
            }
        }
    }
}
