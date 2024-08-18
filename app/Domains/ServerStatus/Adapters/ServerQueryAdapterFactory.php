<?php

namespace App\Domains\ServerStatus\Adapters;

use App\Domains\ServerStatus\Exceptions\UnsupportedGameException;
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
