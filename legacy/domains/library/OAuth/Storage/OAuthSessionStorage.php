<?php

namespace Domains\Library\OAuth\Storage;

use Illuminate\Http\Request;
use Illuminate\Session\Store;

final class OAuthSessionStorage implements OAuthStorageContract
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Session key
     *
     * @var string
     */
    private $key = 'oauth-redirect-uri';


    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    

    private function getSession() : Store
    {
        return $this->request->session();
    }

    public function store(string $redirectUri)
    {
        $this->getSession()->put($this->key, $redirectUri);
    }

    public function clear()
    {
        $this->getSession()->remove($this->key);
    }

    public function get() : ?string
    {
        return $this->getSession()->get($this->key);
    }

    public function pop() : ?string
    {
        $value = $this->get();
        $this->clear();

        return $value;
    }

}