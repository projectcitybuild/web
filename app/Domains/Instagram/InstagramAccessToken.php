<?php

namespace App\Domains\Instagram;

final readonly class InstagramAccessToken
{
    public function __construct(
        public string $accessToken,
        public int $expiresIn,
    ) {}
}
