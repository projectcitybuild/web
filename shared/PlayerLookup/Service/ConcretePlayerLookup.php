<?php

namespace Shared\PlayerLookup\Service;

use App\Core\Data\MinecraftUUID;
use App\Core\Data\PlayerIdentifierType;
use Repositories\MinecraftPlayerAliasRepository;
use Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Contracts\Player;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\NonCreatableIdentifierException;

/**
 * @final
 */
class ConcretePlayerLookup implements PlayerLookup
{
    public function __construct(
        private readonly MinecraftPlayerRepository $minecraftPlayerRepository,
        private readonly MinecraftPlayerAliasRepository $minecraftPlayerAliasRepository,
    ) {
    }

    public function find(PlayerIdentifier $identifier): ?Player
    {
        return match ($identifier->gameIdentifierType) {
            PlayerIdentifierType::MINECRAFT_UUID => $this->minecraftPlayerRepository->getByUUID(new MinecraftUUID($identifier->key)),
            PlayerIdentifierType::PCB_PLAYER_ID => $this->minecraftPlayerRepository->getById($identifier->key),
        };
    }

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        return $this->find($identifier)
            ?? $this->create(identifier: $identifier, playerAlias: $playerAlias);
    }

    /**
     * @throws NonCreatableIdentifierException
     */
    private function create(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        $player = null;

        switch ($identifier->gameIdentifierType) {
            case PlayerIdentifierType::MINECRAFT_UUID:
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
            case PlayerIdentifierType::PCB_PLAYER_ID:
                throw new NonCreatableIdentifierException();
        }

        return $player;
    }
}
