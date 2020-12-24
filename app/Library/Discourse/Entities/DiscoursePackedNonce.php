<?php

namespace App\Library\Discourse\Entities;

final class DiscoursePackedNonce
{
    /**
     * @var string
     */
    private $sso;

    /**
     * @var string
     */
    private $signature;

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
