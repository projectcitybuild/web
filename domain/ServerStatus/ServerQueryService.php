<?php

namespace Domain\ServerStatus;

use App\Entities\GameType;
use App\Entities\Servers\Models\Server;
use Domain\ServerStatus\Adapters\MinecraftQueryAdapter;
use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\Jobs\ServerQueryJob;
use Domain\ServerStatus\Repositories\ServerStatusRepositoryContract;
use Log;

final class ServerQueryService
{
    /**
     * Queries the given server and returns its current status.
     *
     * This operation will block the current process until the query succeeds or fails
     *
     *
     *
     * @throws UnsupportedGameException
     */
    public function querySynchronously(Server $server, ServerStatusRepositoryContract $serverStatusRepository): ServerQueryResult
    {
        Log::notice('Attempting server status query...', $server->toArray());

        $start = microtime(true);
        $time = time();

        $adapter = $this->getQueryAdapter($server->gameType());
        $status = $adapter->query(
            $server->ip,
            $server->port
        );

        $serverStatusRepository->store($server->getKey(), $status, $time);

        $end = microtime(true) - $start;
        Log::notice('Fetch completed in '.($end / 1000).'ms', $status->toArray());

        return $status;
    }

    /**
     * Queries the given server for its current status.
     *
     * This operation is non-blocking, and queues a job to perform the operation
     * eventually in the future
     */
    public function query(Server $server)
    {
        ServerQueryJob::dispatch($server);
    }

    private function getQueryAdapter(GameType $gameType): ServerQueryAdapter
    {
        switch ($gameType->valueOf()) {
            case GameType::Minecraft:
                return new MinecraftQueryAdapter();
            default:
                throw new UnsupportedGameException($gameType->name().' cannot be queried');
        }
    }
}
