<?php
namespace App\Library\Discord\OAuth;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Support\Environment;


class DiscordOAuthService {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;


    public function __construct(Client $client, Request $request) {
        $this->client = $client;
        $this->request = $request;
    }

    public function redirectToProvider(?string $redirectUri = null) {
        if ($redirectUri === null) {
            $redirectUri = config('services.discord.redirect');
        }
        $this->storeRedirectUri($redirectUri);

        $providerUrl = $this->getAuthCodeUrl($redirectUri);

        return redirect()->to($providerUrl);
    }

    public function getProviderAccount() : DiscordOAuthUser {
        $code = $this->request->get('code');

        if (empty($code)) {
            throw new \Exception('Invalid or missing code from OAuth provider');
        }

        $token = $this->exchangeAuthCodeForToken($code);
        $user  = $this->getUser($token->getAccessToken());

        return DiscordOAuthUser::fromJSON($user);
    }

    /**
     * Generates an URL to the provider's login
     *
     * @param string $redirectUri
     * @return string
     */
    private function getAuthCodeUrl(string $redirectUri) : string {
        $baseUrl = 'https://discordapp.com/api/oauth2/authorize';

        $rawParameters = [
            'client_id'     => config('services.discord.client_id'),
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'identify email',
        ];

        // generate an urlencoded string using RFC3986, 
        // because we need spaces to be percent encoded 
        // (%20), and not the default plus encoded (+)
        $parameters = http_build_query($rawParameters, null, '&', PHP_QUERY_RFC3986);

        return $baseUrl . '?' . $parameters;
    }

    /**
     * Exchanges an 'application code' for an
     * access + refresh token pair
     *
     * @param string $code
     * @return DiscordOAuthToken
     */
    private function exchangeAuthCodeForToken(string $code) : DiscordOAuthToken {
        $response = $this->client->post('https://discordapp.com/api/oauth2/token', [
            'form_params' => [
                'client_id'     => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->retrieveRedirectUri(),
            ],
        ]);

        $json  = json_decode($response->getBody(), true);
        $token = DiscordOAuthToken::fromJSON($json);

        return $token;
    }

    /**
     * Returns data for a provider account
     * that has been authorised
     *
     * @param string $accessToken
     * @return void
     */
    private function getUser(string $accessToken) {
        $response = $this->client->get('https://discordapp.com/api/users/@me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $json = json_decode($response->getBody(), true);
        
        return $json;
    }

    private function storeRedirectUri(string $redirectUri) {
        session()->put('discord_redirect_uri', $redirectUri);
    }

    private function retrieveRedirectUri() : string {
        return session()->get('discord_redirect_uri');
    }

}