<?php

namespace App\Domains\ServerStatus\Adapters;

use App\Core\Data\GameType;
use App\Domains\ServerStatus\Exceptions\UnsupportedGameException;

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
