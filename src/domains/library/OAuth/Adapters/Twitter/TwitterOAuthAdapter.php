<?php
namespace Domains\Library\OAuth\Adapters\Twitter;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Domains\Library\OAuth\OAuthProviderContract;
use Domains\Library\OAuth\OAuthUser;


class TwitterOAuthAdapter implements OAuthProviderContract
{
    private function getConnection() : TwitterOAuth
    {
        return new TwitterOAuth(
            config('services.twitter.client_id'),
            config('services.twitter.client_secret'),
            config('services.twitter.access_token'),
            config('services.twitter.access_secret')
        );
    }
    
    public function requestProviderLoginUrl(string $redirectUri) : string
    {
        if ($redirectUri === null) {
            $redirectUri = config('services.twitter.redirect');
        }

        $connection = $this->getConnection();

        $accessToken = $connection->oauth('oauth/request_token', [
            'oauth_callback' => $redirectUri,
        ]);

        $url = $connection->url('oauth/authorize', [
            'oauth_token' => $accessToken['oauth_token'], 
        ]);

        return $url;
    }

    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser
    {
        $connection = $this->getConnection();

        $credentials = $connection->get('account/verify_credentials', [
            'include_email' => 'true',
        ]);

        return new OAuthUser('twitter',
                             $credentials->email,
                             $credentials->name,
                             $credentials->id);
    }

    // private function getRequestToken(string $redirectUri) : string 
    // {
    //     $rawHeaderParams = [
    //         'oauth_consumer_key' => config('services.twitter.client_id'),
    //         'oauth_nonce' => time(),
    //         'oauth_signature_method' => 'HMAC-SHA1',
    //         'oauth_timestamp' => time(),
    //         'oauth_token' => config('services.twitter.access_token'),
    //         'oauth_version' => '1.0',
    //     ];

    //     $pairs = [];
    //     foreach($rawHeaderParams as $key => $value) {
    //         $encodedKey = rawurlencode($key);
    //         $encodedValue = rawurlencode($value);

    //         $pairs[$encodedKey] = $encodedValue;
    //     }
    //     uksort($pairs, 'strcmp');
        
    //     $signatureParams = [];
    //     foreach($pairs as $key => $value) {
    //         $signatureParams[] = $key . '=' . $value;
    //     }
    //     $payload = implode('&', $signatureParams);

    //     $encodedRequestUrl = rawurlencode('https://api.twitter.com/oauth/request_token');
    //     $rawSignature = 'POST&' . $encodedRequestUrl . '&' . rawurlencode($payload);
        
    //     $clientSecret = rawurlencode(config('services.twitter.client_secret'));
    //     $tokenSecret  = rawurlencode(config('services.twitter.access_secret'));
    //     $signingKey   = $clientSecret . '&' . $tokenSecret;

    //     $signedSignature = hash_hmac('sha1', $rawSignature, $signingKey);
        

    //     $headerPairs = [];
    //     $headerPairs[] = 'oauth_callback="'.rawurlencode($redirectUri).'"';
    //     $headerPairs[] = 'oauth_signature="'.$signedSignature.'"';

    //     foreach($rawHeaderParams as $key => $value) {
    //         $headerPairs[] = $key.'="'.rawurlencode($value).'"';
    //     }
    //     uksort($headerPairs, 'strcmp');

    //     $header = 'OAuth ' . implode(',', $headerPairs);

    //     $response = $this->client->post('https://api.twitter.com/oauth/request_token', [
    //         'headers' => [
    //             'Authorization' => $header,
    //         ],
    //     ]);

    //     $json = json_decode($response->getBody(), true);
    //     dd($json);

    //     return $json;
    // }

}