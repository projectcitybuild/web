<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Data\MinecraftUUID;
use App\Core\Data\PlayerIdentifierType;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Exceptions\NonCreatableIdentifierException;
use App\Core\Domains\PlayerLookup\Player;
use App\Models\MinecraftPlayer;

/** @deprecated */
class ConcretePlayerLookup implements PlayerLookup
{
    public function find(PlayerIdentifier $identifier): ?Player
    {
        return match ($identifier->gameIdentifierType) {
            PlayerIdentifierType::MINECRAFT_UUID => MinecraftPlayer::where('uuid', $identifier->key)->first(),
            PlayerIdentifierType::PCB_PLAYER_ID => MinecraftPlayer::where('player_minecraft_id', $identifier->key)->first(),
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
                $player = MinecraftPlayer::create([
                    'uuid' => $uuid->rawValue(),
                    'alias' => $playerAlias,
                ]);
                break;
            case PlayerIdentifierType::PCB_PLAYER_ID:
                throw new NonCreatableIdentifierException();
        }

        return $player;
    }
}
