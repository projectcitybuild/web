<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditArrayEntryData extends Data
{
    public function __construct(
        public string $state,
        public string $text,
    ) {}
}
