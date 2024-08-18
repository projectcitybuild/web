<?php

namespace App\Core\Domains\Tokens\Adapters;

use App\Core\Domains\Tokens\TokenGenerator;

final class StubTokenGenerator implements TokenGenerator
{
    public function __construct(
        private string $token,
    ) {
    }

    public function make(?string $with = null): string
    {
        return $this->token;
    }
}
