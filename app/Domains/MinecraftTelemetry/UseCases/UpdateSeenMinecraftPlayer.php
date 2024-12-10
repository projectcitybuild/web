<?php

namespace App\Domains\MinecraftTelemetry\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;

final class UpdateSeenMinecraftPlayer
{
    public function execute(MinecraftUUID $uuid, string $alias): void
    {
        $player = MinecraftPlayer::firstOrCreate(
            uuid: $uuid,
            alias: $alias,
        );
        $minecraftPlayer = $player->getRawModel();
        $minecraftPlayer->last_seen_at = now();
        $minecraftPlayer->save();
    }
}
