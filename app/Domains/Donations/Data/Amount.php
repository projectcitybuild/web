<?php

namespace App\Domains\Donations\Data;

class Amount
{
    public function __construct(
        public readonly string $currency,
        public readonly int $value,
    ) {}
}
