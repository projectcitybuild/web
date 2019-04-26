<?php

namespace App\Library\OAuth\Adapters\Google;

use App\Library\OAuth\Adapters\OAuthTwoStepProvider;
use App\Library\OAuth\Entities\OAuthUser;
use App\Library\OAuth\Entities\OAuthToken;

final class GoogleOAuthAdapter extends OAuthTwoStepProvider
{
    /**
     * @inheritDoc
     */
    protected $authUrl = 'https://accounts.google.com/o/oauth2/auth';

    /**
     * @inheritDoc
     */
    protected $tokenUrl = 'https://accounts.google.com/o/oauth2/token';

    /**
     * @inheritDoc
     */
    protected $userUrl = 'https://www.googleapis.com/plus/v1/people/me';

    
    /**
     * @inheritDoc
     */
    protected function getAuthRequestParams(string $redirectUri) : array
    {
        return [
            'client_id'     => config('services.google.client_id'),
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid profile email',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTokenRequestParams(string $redirectUri, string $authCode) : array
    {
        return [
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
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
        $googleUser = GoogleOAuthUser::fromJSON($json);

        return new OAuthUser(
            'google', 
            $googleUser->getFirstEmail(), 
            $googleUser->getDisplayName(), 
            $googleUser->getId()
        );
    }

    /**
     * @inheritDoc
     */
    protected function makeToken(array $json) : OAuthToken
    {
        return new OAuthToken(
            $json['access_token'],
            $json['token_type'],
            $json['expires_in'],
            $json['id_token'],
            $json['scope']
        );
    }
}