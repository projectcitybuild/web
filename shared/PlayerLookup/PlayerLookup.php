<?php

namespace Shared\PlayerLookup;

use App\Entities\MinecraftUUID;
use App\Entities\Models\GameIdentifierType;
use App\Entities\Repositories\MinecraftPlayerAliasRepository;
use App\Entities\Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Contracts\Player;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

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
                $player = $this->minecraftPlayerRepository->getByUuid($uuid);
                break;
        }

        return $player;
    }

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        $player = $this->find($identifier);

        if ($player === null) {
            $player = $this->minecraftPlayerRepository->store($identifier->key);

            if (! empty($playerAlias)) {
                $this->minecraftPlayerAliasRepository->store(
                    minecraftPlayerId: $player->getKey(),
                    alias: $playerAlias,
                    registeredAt: now(),
                );
            }
        }

        return $player;
    }
}
