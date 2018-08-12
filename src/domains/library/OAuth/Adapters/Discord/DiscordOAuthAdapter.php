<?php
namespace Domains\Library\OAuth\Adapters\Discord;

use Domains\Library\OAuth\Adapters\OAuthTwoStepProvider;
use Domains\Library\OAuth\OAuthUser;
use Domains\Library\OAuth\OAuthToken;

class DiscordOAuthAdapter extends OAuthTwoStepProvider
{
    /**
     * @inheritDoc
     */
    protected $authUrl = 'https://discordapp.com/api/oauth2/authorize';

    /**
     * @inheritDoc
     */
    protected $tokenUrl = 'https://discordapp.com/api/oauth2/token';

    /**
     * @inheritDoc
     */
    protected $userUrl = 'https://discordapp.com/api/users/@me';


    /**
     * @inheritDoc
     */
    protected function getAuthRequestParams(string $redirectUri) : array
    {
        return [
            'client_id'     => config('services.discord.client_id'),
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'identify email',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTokenRequestParams(string $redirectUri, string $authCode) : array
    {
        return [
            'client_id'     => config('services.discord.client_id'),
            'client_secret' => config('services.discord.client_secret'),
            'grant_type'    => 'authorization_code',
            'code'          => $authCode,
            'redirect_uri'  => $redirectUri,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getUserRequestParams() : array
    {
        return [];
    }
    
    /**
     * @inheritDoc
     */
    protected function makeUser(array $json) : OAuthUser
    {
        $discordUser = DiscordOAuthUser::fromJSON($json);

        return new OAuthUser('discord', 
                             $discordUser->getEmail(), 
                             $discordUser->getUsername(), 
                             $discordUser->getId());
    }

    /**
     * @inheritDoc
     */
    protected function makeToken(array $json) : OAuthToken
    {
        return new OAuthToken($json['access_token'],
                              $json['token_type'],
                              $json['expires_in'],
                              $json['refresh_token'],
                              $json['scope']);
    }
}
