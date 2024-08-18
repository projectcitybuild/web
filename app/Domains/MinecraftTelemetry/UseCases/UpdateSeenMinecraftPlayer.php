<?php

namespace App\Domains\MinecraftTelemetry\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\MinecraftPlayer;
use Repositories\MinecraftPlayerAliasRepository;

final class UpdateSeenMinecraftPlayer
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly MinecraftPlayerAliasRepository $aliasRepository,
    ) {
    }

    public function execute(string $uuid, string $alias): void
    {
        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid)
        );

        /** @var MinecraftPlayer $player */
        $minecraftPlayer = $player->getRawModel();

        $now = now();

        $minecraftPlayer->last_seen_at = $now;
        $minecraftPlayer->save();

        if (! $minecraftPlayer->hasAlias($alias)) {
            $this->aliasRepository->store(
                minecraftPlayerId: $minecraftPlayer->getKey(),
                alias: $alias,
                registeredAt: $now,
            );
        }
    }
}
