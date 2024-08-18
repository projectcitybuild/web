<?php

namespace App\Core\Domains\Random\Adapters;

use App\Core\Domains\Random\RandomStringGenerator;

final class RandomStringMock implements RandomStringGenerator
{
    public function __construct(
        private string $output,
    ) {
    }

    public function generate(int $length): string
    {
        return $this->output;
    }
}
