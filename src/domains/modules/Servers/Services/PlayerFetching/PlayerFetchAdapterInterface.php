<?php
namespace Domains\Modules\Servers\Services\PlayerFetching;

interface PlayerFetchAdapterInterface
{

    /**
     * Returns the unique identifiers for a list of players
     *
     * @param array $aliases
     *
     * @return array
     */
    public function getUniqueIdentifiers(array $aliases = []) : array;

    /**
     * Creates player models specific to this game
     *
     * @param array $identifiers
     *
     * @return array
     */
    public function createPlayers(array $identifiers) : array;
}
