<?php

namespace Domain\ServerStatus\Adapters;

use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Entities\Models\GameType;

/**
 * @final
 */
class ServerQueryAdapterFactory
{
    public function make(GameType $gameType): ServerQueryAdapter
    {
        return match ($gameType) {
            GameType::MINECRAFT => new MinecraftQueryAdapter(),
            default => throw new UnsupportedGameException($gameType->name().' cannot be queried'),
        };
    }
}
