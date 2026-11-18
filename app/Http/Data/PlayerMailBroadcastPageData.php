<?php

namespace App\Http\Data;

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use Spatie\LaravelData\Data;

class PlayerMailBroadcastPageData extends Data
{
    public function __construct(
        public int $id,
        public string $subject,
        public string $body,
        public string $sender,
        public PlayerMailBroadcastStatus $status,
        public int $recipients_count,
        public string $created_at,
        public ?string $started_at,
        public ?string $completed_at,
    ) {}
}
