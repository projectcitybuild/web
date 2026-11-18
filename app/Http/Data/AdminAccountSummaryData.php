<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAccountSummaryData extends Data
{
    public function __construct(
        public string $name,
        public ?string $avatarUuid,
    ) {}
}
