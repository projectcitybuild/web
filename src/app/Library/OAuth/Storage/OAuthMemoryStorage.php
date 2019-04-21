<?php

namespace Domains\Library\OAuth\Storage;

/**
 * For testing purposes
 */
final class OAuthMemoryStorage implements OAuthStorageContract
{
    /**
     * @var string
     */
    private $value;


    public function store(string $redirectUri)
    {
        $this->value = $redirectUri;
    }

    public function clear()
    {
        $this->value = null;
    }

    public function get() : string
    {
        return $this->value;
    }

    public function pop() : string
    {
        $value = $this->value;
        $this->clear();

        return $value;
    }

}