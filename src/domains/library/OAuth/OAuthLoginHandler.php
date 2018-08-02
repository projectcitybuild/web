<?php
namespace Domains\Library\OAuth;

use Domains\Library\OAuth\Storage\OAuthStorageContract;
use Illuminate\Http\RedirectResponse;
use Infrastructure\Environment;
use Illuminate\Http\Request;

class OAuthLoginHandler 
{
    /**
     * @var OAuthProviderContract
     */
    private $provider;

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


    public function __construct(OAuthStorageContract $cache, 
                                OAuthAdapterFactory $adapterFactory, 
                                Request $request)
    {
        $this->cache = $cache;
        $this->adapterFactory = $adapterFactory;
        $this->request = $request;
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

        $authCode = $this->request->get('code');
        $token = $this->request->get('oauth_token');  // for Twitter OAuth

        if (empty($authCode) && empty($token)) {
            throw new \Exception('Invalid or missing auth code from OAuth provider');
        }
        
        $redirectUri = $this->cache->pop();
        $user = $this->provider->requestProviderAccount($redirectUri, $authCode ?: $token);

        return $user;
    }

}