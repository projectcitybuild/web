<?php

namespace App\Library\Discourse\Entities;

final class DiscoursePackedNonce
{
    private string $sso;

    private string $signature;

    public function __construct(string $sso, string $signature)
    {
        $this->sso = $sso;
        $this->signature = $signature;
    }

    public function getSSO(): string
    {
        return $this->sso;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}
