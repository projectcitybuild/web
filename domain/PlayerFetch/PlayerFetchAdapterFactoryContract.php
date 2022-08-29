<?php

namespace Domain\PlayerFetch;

use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Entities\Models\GameType;

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
