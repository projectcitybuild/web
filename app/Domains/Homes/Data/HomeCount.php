<?php

namespace App\Domains\Homes\Data;

use InvalidArgumentException;

class HomeCount
{
    public function __construct(
        readonly int $used,
        readonly int $allowed,
        readonly array $sources,
    ) {
        foreach ($sources as $key => $value) {
            if (!is_string($key) || !is_int($value)) {
                throw new InvalidArgumentException('sources must be of type [string => int]');
            }
        }
    }

    public function atLimit(): bool
    {
        return $this->used >= $this->allowed;
    }
}
