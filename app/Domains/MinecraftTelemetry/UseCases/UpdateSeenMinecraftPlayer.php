<?php

namespace App\Domains\MinecraftTelemetry\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;

final class UpdateSeenMinecraftPlayer
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    public function execute(string $uuid, string $alias): void
    {
        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid)
        );
        $minecraftPlayer = $player->getRawModel();
        $minecraftPlayer->last_seen_at = now();
        $minecraftPlayer->alias = $alias;
        $minecraftPlayer->save();
    }
}
