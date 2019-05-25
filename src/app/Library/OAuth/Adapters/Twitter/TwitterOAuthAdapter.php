<?php

namespace App\Library\OAuth\Adapters\Twitter;

use App\Library\OAuth\OAuthProviderContract;
use App\Library\OAuth\Entities\OAuthUser;
// use Abraham\TwitterOAuth\TwitterOAuth;

final class TwitterOAuthAdapter implements OAuthProviderContract
{
//     private function getConnection() : TwitterOAuth
//     {
//         return new TwitterOAuth(
//             config('services.twitter.client_id'),
//             config('services.twitter.client_secret'),
//             config('services.twitter.access_token'),
//             config('services.twitter.access_secret')
//         );
//     }
    
    public function requestProviderLoginUrl(string $redirectUri) : string
    {
        throw new \Exception('Not implemented');
//         if ($redirectUri === null) {
//             $redirectUri = config('services.twitter.redirect');
//         }

//         $connection = $this->getConnection();

//         $accessToken = $connection->oauth('oauth/request_token', [
//             'oauth_callback' => $redirectUri,
//         ]);

//         $url = $connection->url('oauth/authorize', [
//             'oauth_token' => $accessToken['oauth_token'], 
//         ]);

//         return $url;
    }

    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser
    {
        throw new \Exception('Not implemented');
//         $connection = $this->getConnection();

//         $credentials = $connection->get('account/verify_credentials', [
//             'include_email' => 'true',
//         ]);

//         return new OAuthUser('twitter',
//                              $credentials->email,
//                              $credentials->name,
//                              $credentials->id);
    }

}