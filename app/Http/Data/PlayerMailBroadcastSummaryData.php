<?php

namespace App\Http\Data;

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use Spatie\LaravelData\Data;

class PlayerMailBroadcastSummaryData extends Data
{
    public function __construct(
        public int $id,
        public string $subject,
        public string $sender,
        public int $recipients_count,
        public PlayerMailBroadcastStatus $status,
        public string $created_at,
    ) {}
}
