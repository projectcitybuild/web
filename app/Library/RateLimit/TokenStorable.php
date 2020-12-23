<?php

namespace App\Library\RateLimit;

interface TokenStorable
{
    public function bootstrap(): void;

    public function deserialize(): TokenState;

    public function serialize(TokenState $data): void;
}
