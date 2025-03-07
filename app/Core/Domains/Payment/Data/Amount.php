<?php

namespace App\Core\Domains\Payment\Data;

class Amount
{
    public function __construct(
        public readonly string $currency,
        public readonly int $value,
    ) {}
}
