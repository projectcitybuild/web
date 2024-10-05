<?php

namespace App\Core\Domains\SecureTokens;

interface SecureTokenGenerator
{
    /**
     * Generates a hashed string.
     *
     * Implementors of this interface are not guaranteed to be collision free
     */
    public function make(): string;
}
