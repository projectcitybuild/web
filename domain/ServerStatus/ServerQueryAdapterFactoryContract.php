<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Entities\Models\GameType;

interface ServerQueryAdapterFactoryContract
{
    /**
     * Instantiates an appropriate adapter to perform server
     * status querying of the given game.
     *
     * @throws UnsupportedGameException
     */
    public function make(GameType $gameType): ServerQueryAdapter;
}
