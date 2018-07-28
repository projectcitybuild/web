<?php
namespace Domains\Library\OAuth;

use Illuminate\Http\RedirectResponse;


class OAuthHandler 
{
    /**
     * @var OAuthProviderContract
     */
    private $provider;

    /**
     * @var OAuthCache
     */
    private $cache;


    public function __construct(OAuthProviderContract $provider, OAuthCache $cache)
    {
        $this->provider = $provider;
        $this->cache = $cache;
    }

    public function redirect(string $redirectUri) : RedirectResponse
    {
        $this->cache->store($redirectUri);

        $providerLoginUrl = $this->provider->requestProviderLoginUrl($redirectUri);
        return redirect()->to($providerLoginUrl);
    }

    public function handleCallback() : OAuthUser
    {
        $redirectUri = $this->cache->pop();
        $user = $this->provider->requestProviderAccount($redirectUri);

        return $user;
    }

}