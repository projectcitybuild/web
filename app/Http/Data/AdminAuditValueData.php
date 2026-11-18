<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditValueData extends Data
{
    public function __construct(
        public string $text,
        public ?string $href,
        public string $kind,
    ) {}
}
