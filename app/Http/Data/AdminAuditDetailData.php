<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AdminAuditDetailData extends Data
{
    /**
     * @param  list<AdminAuditChangeData>  $changes
     */
    public function __construct(
        public int $id,
        public string $actor,
        public AdminAuditValueData $subject,
        public string $event,
        public string $createdAt,
        public array $changes,
    ) {}
}
