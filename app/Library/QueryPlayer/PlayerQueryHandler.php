<?php

namespace App\Library\QueryPlayer;

class PlayerQueryHandler
{
    private PlayerQueryAdapterContract $adapter;

    public function setAdapter(PlayerQueryAdapterContract $adapter): void
    {
        $this->adapter = $adapter;
    }

    public function query(array $aliases): array
    {
        $identifiers = $this->adapter->getUniqueIdentifiers($aliases);
        return $this->adapter->createPlayers($identifiers);
    }
}
