<?php

namespace App\Library\QueryServer;

interface ServerQueryAdapterContract
{
    /**
     * Queries the given server for its status
     *
     * @return QueryResult
     */
    public function query(string $ip, ?string $port = null): ServerQueryResult;
}
