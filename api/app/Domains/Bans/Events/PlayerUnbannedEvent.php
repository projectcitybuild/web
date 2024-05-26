<?php

namespace App\Domains\Bans\Events;

use App\Models\PlayerBan;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerUnbannedEvent implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public PlayerBan $player,
    ) {}
}
