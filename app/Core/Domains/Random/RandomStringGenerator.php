<?php

namespace App\Core\Domains\Random;

interface RandomStringGenerator
{
    public function generate(int $length): string;
}
