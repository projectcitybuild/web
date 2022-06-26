<?php

namespace Domain\PlayerFetch;

use Domain\PlayerFetch\Adapters\MojangUUIDFetchAdapter;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Entities\Models\GameType;
use Illuminate\Support\Facades\App;

final class PlayerFetchAdapterFactory implements PlayerFetchAdapterFactoryContract
{
    public function make(GameType $gameType): PlayerFetchAdapter
    {
        return match ($gameType) {
            GameType::MINECRAFT => App::make(MojangUUIDFetchAdapter::class),
            default => throw new UnsupportedGameException($gameType->name().' is not supported'),
        };
    }
}
