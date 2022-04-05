<?php

namespace Library\Tokens;

interface TokenGenerator
{
    /**
     * Generates a hashed string.
     *
     * Implementors of this interface are not guaranteed to be
     * collision free
     */
    function make(): string;
}
