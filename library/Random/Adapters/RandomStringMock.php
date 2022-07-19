<?php

namespace Library\Random\Adapters;

use Library\Random\RandomStringGenerator;

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
