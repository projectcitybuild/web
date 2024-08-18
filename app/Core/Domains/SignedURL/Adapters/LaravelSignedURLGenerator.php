<?php

namespace App\Core\Domains\SignedURL\Adapters;

use App\Core\Domains\SignedURL\SignedURLGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class LaravelSignedURLGenerator implements SignedURLGenerator
{
    public function make(
        string $routeName,
        array $parameters = [],
    ): string {
        return URL::signedRoute(
            name: $routeName,
            parameters: $parameters,
        );
    }

    public function makeTemporary(
        string $routeName,
        Carbon $expiresAt,
        array $parameters = [],
    ): string {
        return URL::temporarySignedRoute(
            name: $routeName,
            expiration: $expiresAt,
            parameters: $parameters,
        );
    }
}
