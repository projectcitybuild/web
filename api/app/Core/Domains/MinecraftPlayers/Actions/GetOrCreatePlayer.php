<?php

namespace App\Core\Domains\MinecraftPlayers\Actions;

use App\Core\Domains\MinecraftPlayers\Events\PlayerCreatedEvent;
use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use App\Models\Player;

class GetOrCreatePlayer
{
    /**
     * Returns a player for the given UUID.
     * If the player does not already exist, it will be created first.
     *
     * @param MinecraftUUID $uuid
     * @return Player
     */
    public function call(MinecraftUUID $uuid): Player
    {
        // __invoke would be a better method name, but IDEs struggle to infer
        // the class to be callable

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
