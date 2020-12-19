<?php

namespace App\Library\Discourse\Entities;

final class DiscourseNonce
{
    /**
     * @var string
     */
    private $nonce;

    /**
     * @var string
     */
    private $redirectUri;

    
    public function __construct(string $nonce, string $redirectUri)
    {
        $this->nonce = $nonce;
        $this->redirectUri = $redirectUri;
    }

    public function getNonce() : string
    {
        return $this->nonce;
    }

    public function getRedirectUri() : string
    {
        return $this->redirectUri;
    }
}