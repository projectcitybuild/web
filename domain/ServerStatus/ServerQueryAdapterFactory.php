<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Adapters\MinecraftQueryAdapter;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Entities\Models\GameType;
use Illuminate\Support\Facades\App;

final class ServerQueryAdapterFactory
{
    public function make(GameType $gameType): ServerQueryAdapter
    {
        return match ($gameType) {
            GameType::MINECRAFT => new MinecraftQueryAdapter(),
            default => throw new UnsupportedGameException($gameType->name() . ' cannot be queried'),
        };
    }
}
