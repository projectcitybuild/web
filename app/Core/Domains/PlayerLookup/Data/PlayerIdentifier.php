<?php

namespace App\Core\Domains\PlayerLookup\Data;

use App\Core\Data\PlayerIdentifierType;
use App\Core\Domains\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;

/** @deprecated */
final class PlayerIdentifier
{
    /**
     * @param  string  $key Identifier for the identifier type (eg. for Minecraft this will be a UUID)
     * @param  PlayerIdentifierType  $gameIdentifierType Identifier type for a supported game
     */
    public function __construct(
        public string $key,
        public PlayerIdentifierType $gameIdentifierType,
    ) {
    }

    /**
     * @throws InvalidMinecraftUUIDException if UUID string is empty
     */
    public static function minecraftUUID(string $uuid): PlayerIdentifier
    {
        if (empty($uuid)) {
            throw new InvalidMinecraftUUIDException();
        }

        return new PlayerIdentifier(
            key: $uuid,
            gameIdentifierType: PlayerIdentifierType::MINECRAFT_UUID,
        );
    }

    public static function pcbAccountId(int $id): PlayerIdentifier
    {
        return new PlayerIdentifier(
            key: $id,
            gameIdentifierType: PlayerIdentifierType::PCB_PLAYER_ID,
        );
    }
}
