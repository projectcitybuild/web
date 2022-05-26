<?php

namespace Shared\PlayerLookup\Entities;

use Entities\Models\GameIdentifierType;

/**
 * TODO: this class is given both database IDs and MC UUIDs as the same type, they should be split.
 */
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

    public static function minecraftUUID(string $uuid): PlayerIdentifier
    {
        return new PlayerIdentifier(
            key: $uuid,
            gameIdentifierType: GameIdentifierType::MINECRAFT_UUID,
        );
    }
}
