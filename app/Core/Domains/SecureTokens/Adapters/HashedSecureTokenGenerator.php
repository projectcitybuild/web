<?php

namespace App\Core\Domains\SecureTokens\Adapters;

use App\Core\Domains\SecureTokens\SecureTokenGenerator;

/**
 * A TokenGenerator that creates a cryptographically-safe
 * series of bytes and further hashes the result with our
 * app key.
 */
final class HashedSecureTokenGenerator implements SecureTokenGenerator
{
    private mixed $byteGenerator;

    public function __construct(
        $byteGenerator = null
    ) {
        $this->byteGenerator = $byteGenerator
            ?? fn () => bin2hex(random_bytes(length: 64));
    }

    /**
     * @throws \Exception if an appropriate source of randomness cannot be found.
     */
    public function make(): string
    {
        return hash_hmac(
            algo: 'sha256',
            data: $this->byteGenerator->call($this),
            key: config('app.key'),
        );
    }
}
