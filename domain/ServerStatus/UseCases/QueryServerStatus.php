<?php

namespace Domain\ServerStatus\UseCases;

use App\Models\Server;
use Domain\ServerStatus\Adapters\ServerQueryAdapterFactory;
use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Illuminate\Support\Facades\Log;
use Repositories\ServerRepository;

final class QueryServerStatus
{
    public function __construct(
        private readonly ServerQueryAdapterFactory $queryAdapterFactory,
        private readonly ServerRepository $serverRepository,
    ) {
    }

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

        $this->serverRepository->updateStatus(
            server: $server,
            status: $status,
            queriedAt: $now,
        );

        $end = microtime(true) - $start;
        Log::info('Server status fetched in '.($end / 1000).'ms', $status->toArray());

        return $status;
    }
}
