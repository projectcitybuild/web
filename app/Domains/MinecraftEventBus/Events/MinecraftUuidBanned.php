<?php

namespace App\Domains\MinecraftEventBus\Events;

use App\Models\GamePlayerBan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MinecraftUuidBanned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public GamePlayerBan $ban,
    ) {}
}
