<?php

namespace Shared\PlayerLookup;

use Entities\Models\GameIdentifierType;
use Entities\Models\MinecraftUUID;
use Entities\Repositories\MinecraftPlayerAliasRepository;
use Shared\PlayerLookup\Contracts\Player;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Repositories\MinecraftPlayerRepository;

/**
 * @final
 */
class PlayerLookup
{
    public function __construct(
        private MinecraftPlayerRepository $minecraftPlayerRepository,
        private MinecraftPlayerAliasRepository $minecraftPlayerAliasRepository,
    ) {}

    public function find(PlayerIdentifier $identifier): ?Player
    {
        $player = null;

        switch ($identifier->gameIdentifierType) {
            case GameIdentifierType::MINECRAFT_UUID:
                $uuid = new MinecraftUUID($identifier->key);
                $player = $this->minecraftPlayerRepository->getByUUID($uuid);
                break;
        }

        return $player;
    }

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        return $this->find($identifier)
            ?? $this->create(identifier: $identifier, playerAlias: $playerAlias);
    }

    private function create(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        $player = null;

        switch ($identifier->gameIdentifierType) {
            case GameIdentifierType::MINECRAFT_UUID:
                $uuid = new MinecraftUUID($identifier->key);
                $player = $this->minecraftPlayerRepository->store($uuid);

                if (! empty($playerAlias)) {
                    $this->minecraftPlayerAliasRepository->store(
                        minecraftPlayerId: $player->getKey(),
                        alias: $playerAlias,
                        registeredAt: now(),
                    );
                }
                break;
        }

        return $player;
    }
}
