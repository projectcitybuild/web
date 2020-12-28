<?php

namespace App\Library\QueryServer;

interface ServerQueryAdapterContract
{
    /**
     * Queries the given server for its status.
     *
     * @param string $port
     *
     * @return QueryResult
     */
    public function query(string $ip, $port = null): ServerQueryResult;
}
