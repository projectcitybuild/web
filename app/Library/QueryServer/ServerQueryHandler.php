<?php

namespace App\Library\QueryServer;

use App\Entities\Servers\Repositories\ServerStatusRepository;
use Illuminate\Support\Facades\Log;

class ServerQueryHandler
{
    private ServerQueryAdapterContract $adapter;

    private ServerStatusRepository $serverStatusRepository;

    private int $lastCreatedId;

    public function __construct(ServerStatusRepository $serverStatusRepository)
    {
        $this->serverStatusRepository = $serverStatusRepository;
    }

    /**
     * Sets the adapter that will perform
     * the server query
     */
    public function setAdapter(ServerQueryAdapterContract $adapter): void
    {
        $this->adapter = $adapter;
    }

    public function getLastCreatedId(): ?int
    {
        return $this->lastCreatedId;
    }

    /**
     * Queries the given server address for
     * its current status and player data
     */
    public function queryServer(int $serverId, string $ip, string $port): ServerQueryResult
    {
        $serverData = [
            'server_id' => $serverId,
            'ip' => $ip,
            'port' => $port,
        ];

        Log::notice('Performing server status fetch...', $serverData);
        $start = microtime(true);

        $status = $this->requestStatus($serverId, $ip, $port);

        $end = microtime(true) - $start;
        Log::notice('Fetch complete ['. ($end / 1000) .'ms]', $serverData);

        return $status;
    }

    private function requestStatus(int $serverId, string $ip, string $port): ServerQueryResult
    {
        $time = time();

        $status = $this->adapter->query($ip, $port);

        Log::info('Received server status', ['status' => $status]);

        $statusRecord = $this->serverStatusRepository->create(
            $serverId,
            $status->isOnline(),
            $status->getNumOfPlayers(),
            $status->getNumOfSlots(),
            $time
        );

        $this->lastCreatedId = $statusRecord->getKey();

        return $status;
    }
}
