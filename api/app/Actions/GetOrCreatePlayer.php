<?php

namespace App\Actions;

use App\Models\Eloquent\Player;
use App\Models\Events\PlayerCreatedEvent;
use App\Models\MinecraftUUID;

class GetOrCreatePlayer
{
    public function run(MinecraftUUID $uuid): Player
    {
        $player = Player::uuid($uuid->rawValue())->first();

        if ($player === null) {
            $player = Player::create(['uuid' => $uuid->rawValue()]);
            PlayerCreatedEvent::dispatch($player);
        }
        return $player;
    }
}
