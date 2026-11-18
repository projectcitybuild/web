<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class ManageHomeRecentActivityData extends Data
{
    public function __construct(
        public string $id,
        public string $actor,
        public ?string $avatarUuid,
        public string $action,
        public string $event,
        public string $subject,
        public string $createdAt,
        public string $when,
    ) {}
}
