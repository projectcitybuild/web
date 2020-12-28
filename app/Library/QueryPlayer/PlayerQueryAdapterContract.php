<?php

namespace App\Library\QueryPlayer;

interface PlayerQueryAdapterContract
{
    /**
     * Fetches a unique player identifier for
     * each name
     *
     * @param array $aliases
     *
     * @return array
     */
    public function getUniqueIdentifiers(array $aliases = []): array;

    /**
     * Creates a player record in PCB
     *
     * @param array $identifiers
     *
     * @return array
     */
    public function createPlayers(array $identifiers): array;
}
