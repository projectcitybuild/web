<?php

namespace Domain\ServerStatus;

use App\Entities\GameType;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;

interface ServerQueryAdapterFactoryContract
{
    /**
     * Instantiates an appropriate adapter to perform server
     * status querying of the given game
     *
     * @param GameType $gameType
     * @return ServerQueryAdapter
     * @throws UnsupportedGameException
     */
    public function make(GameType $gameType): ServerQueryAdapter;
}
