<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class PlayerMailComposePageData extends Data
{
    public function __construct(
        public int $recipientCount,
        public string $sender,
    ) {}
}
