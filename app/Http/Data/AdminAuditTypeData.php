<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditTypeData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
