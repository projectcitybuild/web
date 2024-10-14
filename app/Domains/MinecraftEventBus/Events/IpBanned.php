<?php

namespace App\Domains\MinecraftEventBus\Events;

use App\Models\GameIPBan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IpBanned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public GameIPBan $ban,
    ) {}
}
