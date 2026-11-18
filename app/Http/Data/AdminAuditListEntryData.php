<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditListEntryData extends Data
{
    public function __construct(
        public int $id,
        public string $actor,
        public string $action,
        public string $event,
        public string $subject,
        public string $createdAt,
        public string $showUrl,
    ) {}
}
