<?php

namespace App\Domains\MinecraftTelemetry\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;

final class UpdateSeenMinecraftPlayer
{
    public function execute(MinecraftUUID $uuid, string $alias): MinecraftPlayer
    {
        $player = MinecraftPlayer::firstOrCreate(
            uuid: $uuid,
            alias: $alias,
        );
        $now = now();
        if ($player->last_connected_at === null) {
            $player->last_connected_at = $now;
        }
        $player->last_seen_at = $now;
        $player->save();

        return $player;
    }
}
