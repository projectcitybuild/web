<?php
namespace Domains\Library\OAuth\Adapters;

use Domains\Library\OAuth\OAuthToken;
use Domains\Library\OAuth\OAuthUser;
use Domains\Library\OAuth\OAuthProviderContract;


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


    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    

    protected abstract function makeToken() : OAuthToken;

    protected abstract function makeUser(array $json) : OAuthUser;

    protected abstract function getAuthRequestParams(string $redirectUri) : array;

    protected abstract function getTokenRequestParams(string $redirectUri, string $authCode) : array;


    public function requestProviderLoginUrl(string $redirectUri) : string
    {
        $this->cache->store($redirectUri);

        $providerUrl = $this->buildAuthCodeRequestUrl($redirectUri);

        return $providerUrl;
    }

    public function requestProviderAccount(string $redirectUri) : OAuthUser
    {
        $authCode = $this->request->get('code');

        if (empty($authCode)) {
            throw new \Exception('Invalid or missing auth code from OAuth provider');
        }

        $token = $this->exchangeAuthCodeForToken($redirectUri, $authCode);
        $user  = $this->exchangeTokenForUser($token->getAccessToken());

        return $this->makeUser($user);
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

        return $token;
    }

    private function exchangeTokenForUser(string $accessToken) : OAuthUser
    {
        $response = $this->client->get($this->userUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $json = json_decode($response->getBody(), true);
        $user = $this->makeUser($json);
        
        return $user;
    }
}