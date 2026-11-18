<?php

namespace App\Http\Data;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\LaravelData\Data;

class AdminAccountBadgePivotData extends Data
{
    public function __construct(
        public int $account_id,
        public int $badge_id,
    ) {}

    public static function fromModel(Pivot $pivot): self
    {
        return new self(
            account_id: $pivot->account_id,
            badge_id: $pivot->badge_id,
        );
    }
}
