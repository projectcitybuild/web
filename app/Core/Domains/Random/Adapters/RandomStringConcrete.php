<?php

namespace App\Core\Domains\Random\Adapters;

use App\Core\Domains\Random\RandomStringGenerator;
use Illuminate\Support\Str;

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
