<?php

namespace App\Library\Discourse\Entities;

final class DiscourseNonce
{
    private string $nonce;

    private string $redirectUri;

    public function __construct(string $nonce, string $redirectUri)
    {
        $this->nonce = $nonce;
        $this->redirectUri = $redirectUri;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }
}
