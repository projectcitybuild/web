<?php

namespace App\Core\Support\Laravel\SignedURL\Adapters;

use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use Illuminate\Support\Carbon;

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
