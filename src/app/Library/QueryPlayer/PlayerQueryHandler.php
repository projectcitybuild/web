<?php
namespace Domains\Library\QueryPlayer;

class PlayerQueryHandler
{
    /**
     * @var PlayerQueryAdapterContract
     */
    private $adapter;

    public function setAdapter(PlayerQueryAdapterContract $adapter)
    {
        $this->adapter = $adapter;
    }

    public function query(array $aliases) : array
    {
        $identifiers = $this->adapter->getUniqueIdentifiers($aliases);
        $playerIds = $this->adapter->createPlayers($identifiers);

        return $playerIds;
    }
}