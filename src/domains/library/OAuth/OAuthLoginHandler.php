<?php
namespace Domains\Library\OAuth;

use Domains\Library\OAuth\Storage\OAuthCache;
use Illuminate\Http\RedirectResponse;
use Infrastructure\Environment;

class OAuthLoginHandler 
{
    /**
     * @var OAuthProviderContract
     */
    private $provider;

    /**
     * @var OAuthCache
     */
    private $cache;

    /**
     * @var OAuthAdapterFactory
     */
    private $adapterFactory;


    public function __construct(OAuthCache $cache, OAuthAdapterFactory $adapterFactory)
    {
        $this->cache = $cache;
        $this->adapterFactory = $adapterFactory;
    }

    public function setProvider(string $providerName)
    {
        $this->provider = $this->adapterFactory->make($providerName);
    }

    public function redirectToLogin(string $redirectUri) : RedirectResponse
    {
        if ($this->provider === null) {
            throw new \Exception('No OAuth provider set');
        }

        // Laravel converts localhost into a local IP address,
        // so we need to manually convert it back so that
        // it matches the URLs registered in Twitter, Google, etc
        if (Environment::isDev()) {
            $redirectUri = str_replace('http://192.168.99.100/',
                                       'http://localhost:3000/',
                                       $redirectUri);
        }

        $this->cache->store($redirectUri);

        $providerLoginUrl = $this->provider->requestProviderLoginUrl($redirectUri);
        return redirect()->to($providerLoginUrl);
    }

    public function getOAuthUser() : OAuthUser
    {
        if ($this->provider === null) {
            throw new \Exception('No OAuth provider set');
        }
        
        $redirectUri = $this->cache->pop();
        $user = $this->provider->requestProviderAccount($redirectUri);

        return $user;
    }

}