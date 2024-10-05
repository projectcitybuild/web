<?php

namespace App\Core\Domains\SecureTokens\Adapters;

use App\Core\Domains\SecureTokens\SecureTokenGenerator;

final class StubSecureTokenGenerator implements SecureTokenGenerator
{
    public function __construct(
        private readonly string $token,
    ) {}

    public function make(): string
    {
        return $this->token;
    }
}
