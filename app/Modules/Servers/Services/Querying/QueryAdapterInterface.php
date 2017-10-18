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
     * @param string $player    Player identifier to potentially create an account for
     * @param int $requestTime  Timestamp of the server status query
     * @return void
     */
    public function createGameUser(string $player, int $requestTime);

}