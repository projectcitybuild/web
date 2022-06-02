<?php

namespace Shared\PlayerLookup\Entities;

use Entities\Models\GameIdentifierType;
use Shared\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;

final class PlayerIdentifier
{
    /**
     * @param string $key Identifier for the identifier type (eg. for Minecraft this will be a UUID)
     * @param GameIdentifierType $gameIdentifierType Identifier type for a supported game
     */
    public function __construct(
        public string $key,
        public GameIdentifierType $gameIdentifierType,
    ) {}

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
            gameIdentifierType: GameIdentifierType::MINECRAFT_UUID,
        );
    }
}
