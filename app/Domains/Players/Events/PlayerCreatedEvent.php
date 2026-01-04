<?php

namespace App\Domains\Players\Events;

use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public MinecraftPlayer $player,
    ) {}
}
