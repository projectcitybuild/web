<?php

namespace Domains\Library\OAuth;

use Domains\Library\OAuth\Storage\OAuthStorageContract;
use Domains\Library\OAuth\Entities\OAuthUser;
use App\Environment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Domains\Library\OAuth\Exceptions\OAuthSessionExpiredException;

final class OAuthLoginHandler 
{
    /**
     * @var OAuthStorageContract
     */
    private $cache;

    /**
     * @var OAuthAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var Request
     */
    private $request;


    public function __construct(
        OAuthStorageContract $cache,
        OAuthAdapterFactory $adapterFactory, 
        Request $request
    ) {
        $this->cache = $cache;
        $this->adapterFactory = $adapterFactory;
        $this->request = $request;
    }

    public function redirectToLogin(string $providerName, string $redirectUri) : RedirectResponse
    {
        // Laravel converts localhost into a local IP address,
        // so we need to manually convert it back so that
        // it matches the URLs registered in Twitter, Google, etc
        if (Environment::isDev()) 
        {
            $invalidDevUrls = ['http://192.168.99.100/', 'http://nginx/'];
            foreach ($invalidDevUrls as $invalidUrl)
            {
                $redirectUri = str_replace($invalidUrl, 'http://localhost:3000/', $redirectUri);
            }
        }

        $this->cache->store($redirectUri);

        $provider = $this->adapterFactory->make($providerName);
        $providerLoginUrl = $provider->requestProviderLoginUrl($redirectUri);

        return redirect()->to($providerLoginUrl);
    }

    public function getOAuthUser(string $providerName) : OAuthUser
    {
        $provider = $this->adapterFactory->make($providerName);

        $authCode = $this->request->get('code');
        $token    = $this->request->get('oauth_token');  // for Twitter OAuth

        if (empty($authCode) && empty($token)) 
        {
            throw new \Exception('Invalid or missing auth code from OAuth provider');
        }
        
        $redirectUri = $this->cache->pop();

        // if this is null, it's highly likely the user refreshed the page
        // or returned back to the OAuth login screen - both of which will
        // invalidate the cache; in which case the user needs to start-over
        if ($redirectUri === null)
        {
            throw new OAuthSessionExpiredException('Session has expired. Please start-over from PCB login screen');
        }

        $user = $provider->requestProviderAccount($redirectUri, $authCode ?: $token);

        return $user;
    }

}