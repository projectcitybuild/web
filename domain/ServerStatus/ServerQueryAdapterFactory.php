<?php

namespace Domain\ServerStatus;

use App\Entities\Models\GameType;
use Domain\ServerStatus\Adapters\MinecraftQueryAdapter;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;

final class ServerQueryAdapterFactory implements ServerQueryAdapterFactoryContract
{
    public function make(GameType $gameType): ServerQueryAdapter
    {
        return match ($gameType) {
            GameType::MINECRAFT => new MinecraftQueryAdapter(),
            default => throw new UnsupportedGameException($gameType->name() . ' cannot be queried'),
        };
    }
}
