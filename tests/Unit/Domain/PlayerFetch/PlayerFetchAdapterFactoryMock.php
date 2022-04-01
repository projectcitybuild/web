<?php

namespace Tests\Unit\Domain\PlayerFetch;

use App\Entities\Models\GameType;
use Domain\PlayerFetch\PlayerFetchAdapter;
use Domain\PlayerFetch\PlayerFetchAdapterFactoryContract;

final class PlayerFetchAdapterFactoryMock implements PlayerFetchAdapterFactoryContract
{
    private PlayerFetchAdapter $adapter;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    public function make(GameType $gameType): PlayerFetchAdapter
    {
        return $this->adapter;
    }
}
