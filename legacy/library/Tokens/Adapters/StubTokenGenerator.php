<?php

namespace Library\Tokens\Adapters;

use Library\Tokens\TokenGenerator;

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
