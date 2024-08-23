<?php

namespace App\Domains\ServerStatus\Adapters;

use App\Domains\ServerStatus\Data\ServerQueryResult;

interface ServerQueryAdapter
{
    /**
     * Queries the given server for its current status.
     *
     * @param  null  $port
     */
    public function query(string $ip, $port = null): ServerQueryResult;
}
