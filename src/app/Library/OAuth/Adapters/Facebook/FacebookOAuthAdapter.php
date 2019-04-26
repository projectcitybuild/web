<?php

namespace App\Library\OAuth\Adapters\Facebook;

use App\Library\OAuth\Adapters\OAuthTwoStepProvider;
use App\Library\OAuth\Entities\OAuthUser;
use App\Library\OAuth\Entities\OAuthToken;

final class FacebookOAuthAdapter extends OAuthTwoStepProvider
{
    /**
     * @inheritDoc
     */
    protected $authUrl = 'https://www.facebook.com/v3.1/dialog/oauth';

    /**
     * @inheritDoc
     */
    protected $tokenUrl = 'https://graph.facebook.com/v3.1/oauth/access_token';

    /**
     * @inheritDoc
     */
    protected $userUrl = 'https://graph.facebook.com/v3.1/me';

    
    /**
     * @inheritDoc
     */
    protected function getAuthRequestParams(string $redirectUri) : array
    {
        return [
            'client_id'     => config('services.facebook.client_id'),
            'redirect_uri'  => $redirectUri,
            'scope'         => 'email',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTokenRequestParams(string $redirectUri, string $authCode) : array
    {
        return [
            'client_id'     => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri'  => $redirectUri,
            'code'          => $authCode,
        ];
    }
    
    /**
     * @inheritDoc
     */
    protected function getUserRequestParams() : array
    {
        return [
            'fields' => 'name,email,gender,verified,link',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function makeUser(array $json) : OAuthUser
    {
        $facebookUser = FacebookOAuthUser::fromJSON($json);

        return new OAuthUser(
            'facebook', 
            $facebookUser->getEmail(), 
            $facebookUser->getName(), 
            $facebookUser->getId()
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
            '',
            ''
        );
    }
}