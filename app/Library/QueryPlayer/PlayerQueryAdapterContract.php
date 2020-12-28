<?php

namespace App\Library\QueryPlayer;

interface PlayerQueryAdapterContract
{
    /**
     * Fetches a unique player identifier for
     * each name.
     */
    public function getUniqueIdentifiers(array $aliases = []): array;

    /**
     * Creates a player record in PCB.
     */
    public function createPlayers(array $identifiers): array;
}
