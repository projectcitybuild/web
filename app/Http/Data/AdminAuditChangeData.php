<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditChangeData extends Data
{
    /**
     * @param  list<AdminAuditArrayEntryData>|null  $oldEntries
     * @param  list<AdminAuditArrayEntryData>|null  $newEntries
     */
    public function __construct(
        public string $attribute,
        public string $description,
        public AdminAuditValueData $oldValue,
        public AdminAuditValueData $newValue,
        public ?array $oldEntries,
        public ?array $newEntries,
    ) {}
}
