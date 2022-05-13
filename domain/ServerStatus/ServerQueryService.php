<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\Jobs\ServerQueryJob;
use Domain\ServerStatus\Repositories\ServerStatusRepository;
use Entities\Models\Eloquent\Server;
use Illuminate\Support\Facades\Log;

final class ServerQueryService
{
    public function __construct(
        private ServerQueryAdapterFactory $queryAdapterFactory,
        private ServerStatusRepository $serverStatusRepository,
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
        Log::notice('Attempting server status query...', $server->toArray());

        $start = microtime(true);
        $now = now();

        $adapter = $this->queryAdapterFactory->make($server->gameType());
        $status = $adapter->query(
            ip: $server->ip,
            port: $server->port
        );

        $this->serverStatusRepository->update(
            server: $server,
            status: $status,
            queriedAt: $now,
        );

        $end = microtime(true) - $start;
        Log::notice('Fetch completed in '.($end / 1000).'ms', $status->toArray());

        return $status;
    }
}
