<?php

namespace Domain\PlayerFetch;

use App\Entities\Models\GameType;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;

interface PlayerFetchAdapterFactoryContract
{
    /**
     * Instantiates an appropriate adapter to perform player
     * data fetching for the given game type.
     *
     * @throws UnsupportedGameException
     */
    public function make(GameType $gameType): PlayerFetchAdapter;
}
