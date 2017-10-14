<?php
namespace App\Modules\Servers\Services\Querying;

interface QueryAdapterInterface {

    /**
     * Queries the given server for its status
     *
     * @param string $ip
     * @param string $port
     * @return QueryResult
     */
    public function query(string $ip, $port = null) : QueryResult;

    /**
     * Fetches a game-specific, unique identifier for the given player
     *
     * @param object $player
     * @return void
     */
    public function fetchPlayerIdentifier($player);

}