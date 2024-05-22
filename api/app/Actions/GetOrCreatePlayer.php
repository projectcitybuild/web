<?php

namespace App\Actions;

use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerAlias;
use App\Models\Events\PlayerCreatedEvent;
use App\Models\MinecraftUUID;

class GetOrCreatePlayer
{
    public function __invoke(MinecraftUUID $uuid): Player
    {
        $player = Player::uuid($uuid)->first();

        if ($player === null) {
            // TODO: validate it's a real Minecraft UUID?

            $player = Player::create([
                'uuid' => $uuid->trimmed(),
                'last_seen_at' => now(),
            ]);
            PlayerCreatedEvent::dispatch($player);
        } else {
            $player->last_seen_at = now();
            $player->save();
        }

        return $player;
    }
}
