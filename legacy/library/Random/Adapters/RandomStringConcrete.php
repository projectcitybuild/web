<?php

namespace Library\Random\Adapters;

use Illuminate\Support\Str;
use Library\Random\RandomStringGenerator;

/**
 * A wrapper for `Str::random()` because we can't override
 * the output of it in tests
 */
final class RandomStringConcrete implements RandomStringGenerator
{
    public function generate(int $length): string
    {
        return Str::random($length);
    }
}
