<?php

namespace Domain\MinecraftTelemetry\UseCases;

use Entities\Models\Eloquent\MinecraftPlayer;
use Repositories\MinecraftPlayerAliasRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class UpdateSeenMinecraftPlayerUseCase
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly MinecraftPlayerAliasRepository $aliasRepository,
    ) {}

    public function execute(string $uuid, string $alias): void
    {
        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid)
        );

        /** @var MinecraftPlayer $player */
        $minecraftPlayer = $player->getRawModel();

        $now = now();

        $minecraftPlayer->last_synced_at = $now;
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
