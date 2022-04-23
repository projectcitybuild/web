<?php

namespace Library\SignedURL;

use Illuminate\Support\Carbon;

interface SignedURLGenerator
{
    /**
     * Generates a URL that is signed as to prevent parameter tampering.
     *
     * @param string $routeName Route name
     * @param array $parameters Payload to embed in URL
     * @return string URL string
     */
    public function make(
        string $routeName,
        array $parameters = [],
    ): string;

    /**
     * Generates a temporary URL that expires after a given date.
     * The URL is signed as to prevent parameter tampering.
     *
     * @param string $routeName Route name
     * @param Carbon $expiresAt Date at which the URL becomes deactivated
     * @param array $parameters Payload to embed in URL
     * @return string URL string
     */
    public function makeTemporary(
        string $routeName,
        Carbon $expiresAt,
        array $parameters = [],
    ): string;
}
