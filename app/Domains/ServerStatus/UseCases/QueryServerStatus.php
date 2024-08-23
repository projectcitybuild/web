<?php

namespace App\Domains\ServerStatus\UseCases;

use App\Domains\ServerStatus\Adapters\ServerQueryAdapterFactory;
use App\Domains\ServerStatus\Data\ServerQueryResult;
use App\Domains\ServerStatus\Exceptions\UnsupportedGameException;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

final class QueryServerStatus
{
    public function __construct(
        private readonly ServerQueryAdapterFactory $queryAdapterFactory,
    ) {}

    /**
     * Queries the given server and returns its current status.
     *
     * This operation will block the current process until the query succeeds or fails
     *
     * @throws UnsupportedGameException
     */
    public function query(Server $server): ServerQueryResult
    {
        Log::info('Attempting server status query...', $server->toArray());

        $start = microtime(true);
        $now = now();

        $adapter = $this->queryAdapterFactory->make($server->gameType());
        $status = $adapter->query(
            ip: $server->ip,
            port: $server->port
        );

        $server->updateWithStatus($status, queriedAt: $now);

        $end = microtime(true) - $start;
        Log::info('Server status fetched in '.($end / 1000).'ms', $status->toArray());

        return $status;
    }
}
