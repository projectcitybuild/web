<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;

interface ServerQueryAdapter
{
    /**
     * Queries the given server for its current status.
     * @param string $ip
     * @param null $port
     * @return ServerQueryResult
     */
    public function query(string $ip, $port = null): ServerQueryResult;
}
