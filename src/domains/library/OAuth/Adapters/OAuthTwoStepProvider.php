<?php
namespace Domains\Library\OAuth\Adapters;

use Domains\Library\OAuth\OAuthToken;
use Domains\Library\OAuth\OAuthUser;
use Domains\Library\OAuth\OAuthProviderContract;
use Illuminate\Log\Logger;
use GuzzleHttp\Client;


/**
 * An OAuth provider that provides authentication
 * over two steps:
 * 
 * 1. Receives authentication token after login
 * 2. Exchange authentication token for access token
 * 
 * The access token is then used to make api requests
 * on behalf of the user
 */
abstract class OAuthTwoStepProvider implements OAuthProviderContract
{
    /**
     * HTTP client
     * 
     * @var Client
     */
    private $client;

    /**
     * @var Logger
     */
    private $log;

    /**
     * Login URL to redirect the user to
     * when starting the OAuth flow
     *
     * @var string
     */
    protected $authUrl;

    /**
     * API url to exchange authentication code 
     * received from login, for an access token
     *
     * @var string
     */
    protected $tokenUrl;

    /**
     * API url to fetch provider user email, 
     * id, etc
     *
     * @var string
     */
    protected $userUrl;


    public function __construct(Client $client, Logger $logger)
    {
        $this->client = $client;
        $this->log = $logger;
    }
    

    protected abstract function makeToken(array $json) : OAuthToken;

    protected abstract function makeUser(array $json) : OAuthUser;

    protected abstract function getAuthRequestParams(string $redirectUri) : array;

    protected abstract function getTokenRequestParams(string $redirectUri, string $authCode) : array;

    protected abstract function getUserRequestParams() : array;
    

    public function requestProviderLoginUrl(string $redirectUri) : string
    {
        $providerUrl = $this->buildAuthCodeRequestUrl($redirectUri);
        $this->log->debug('Built redirect url: ' . $providerUrl);

        return $providerUrl;
    }

    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser
    {
        $token = $this->exchangeAuthCodeForToken($redirectUri, $authCode);
        $user  = $this->exchangeTokenForUser($token->getAccessToken());

        return $user;
    }

    private function buildAuthCodeRequestUrl(string $redirectUri) : string
    {
        $params = $this->getAuthRequestParams($redirectUri);

        // generate an urlencoded string using RFC3986,
        // because we need spaces to be percent encoded
        // (%20), and not the default plus encoded (+)
        $query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

        return $this->authUrl . '?' . $query;
    }

    /**
     * Exchanges an 'application code' for an
     * access + refresh token pair
     *
     * @param string $authCode
     * @return DiscordOAuthToken
     */
    private function exchangeAuthCodeForToken(string $redirectUri, string $authCode) : OAuthToken
    {
        $params = $this->getTokenRequestParams($redirectUri, $authCode);

        $response = $this->client->post($this->tokenUrl, [
            'form_params' => $params,
        ]);

        $json  = json_decode($response->getBody(), true);
        $token = $this->makeToken($json);

        $this->log->debug('Exchanged auth code for token', [
            'response'  => $json,
            'token'     => $token,
        ]);

        return $token;
    }

    private function exchangeTokenForUser(string $accessToken) : OAuthUser
    {
        $response = $this->client->get($this->userUrl, [
            'query'   => $this->getUserRequestParams(),
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $json = json_decode($response->getBody(), true);
        $user = $this->makeUser($json);

        $this->log->debug('Exchanged token for user data', [
            'response'  => $json,
            'user'     => $user,
        ]);
        
        return $user;
    }
}