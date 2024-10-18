<?php

namespace App\Domains\MinecraftEventBus\Events;

use App\Models\MinecraftPlayer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MinecraftPlayerUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MinecraftPlayer $player,
    ) {}
}
