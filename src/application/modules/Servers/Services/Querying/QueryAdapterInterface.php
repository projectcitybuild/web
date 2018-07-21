<?php
namespace Application\Modules\Servers\Services\Querying;

use Application\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;

interface QueryAdapterInterface
{

    /**
     * Queries the given server for its status
     *
     * @param string $ip
     * @param string $port
     * @return QueryResult
     */
    public function query(string $ip, $port = null) : QueryResult;


    /**
     * Returns a player fetching adapter appropriate to the
     * server's game type
     *
     * @return PlayerFetchAdapterInterface
     */
    public function getPlayerFetchAdapter() : string;
}
