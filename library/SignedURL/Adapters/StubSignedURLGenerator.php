<?php

namespace Library\SignedURL\Adapters;

use Illuminate\Support\Carbon;
use Library\SignedURL\SignedURLGenerator;

final class StubSignedURLGenerator implements SignedURLGenerator
{
    public function __construct(
        private string $outputURL,
    ) {}

    public function make(
        string $routeName,
        array $parameters = [],
    ): string {
        return $this->outputURL;
    }

    public function makeTemporary(
        string $routeName,
        Carbon $expiresAt,
        array $parameters = [],
    ): string {
        return $this->outputURL;
    }
}
