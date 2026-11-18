<?php

namespace App\Http\Data;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\LaravelData\Data;

class AdminAccountRolePivotData extends Data
{
    public function __construct(
        public int $account_id,
        public int $role_id,
    ) {}

    public static function fromModel(Pivot $pivot): self
    {
        return new self(
            account_id: $pivot->account_id,
            role_id: $pivot->role_id,
        );
    }
}
