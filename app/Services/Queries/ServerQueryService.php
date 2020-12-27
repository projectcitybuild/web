<?php

namespace App\Services\Queries;

use App\Entities\GameType;
use App\Entities\Servers\Repositories\ServerStatusPlayerRepository;
use App\Library\QueryServer\ServerQueryResult;
use App\Services\Queries\Entities\ServerJobEntity;
use App\Services\Queries\Jobs\PlayerQueryJob;
use App\Services\Queries\Jobs\ServerQueryJob;

final class ServerQueryService
{
    /**
     * @var ServerStatusPlayerRepository
     */
    private $serverStatusRepository;

    public function __construct(ServerStatusPlayerRepository $serverPlayerRepository)
    {
        $this->serverStatusRepository = $serverPlayerRepository;
    }

    /**
     * Dispatches a job to query a server for its
     * current status and player list.
     *
     *
     * @return void
     */
    public function dispatchQuery(GameType $gameType, int $serverId, string $ip, string $port, bool $isDryRun = false)
    {
        $entity = new ServerJobEntity(
            $gameType->serverQueryAdapter(),
            $gameType->playerQueryAdapter(),
            $gameType->name(),
            $serverId,
            $ip,
            $port,
            $isDryRun
        );
        ServerQueryJob::dispatch($entity);
    }

    /**
     * Receives the result of a server query job, then dispatches a player query job
     * if players were online, to uniquely identify each player and store them in
     * PCB's player database for statistics.
     *
     *
     * @return void
     */
    public function processServerResult(ServerJobEntity $entity, ServerQueryResult $status)
    {
        if ($entity->getIsDryRun()) {
            dump($status);

            return;
        }

        if (count($status->getPlayerList()) > 0) {
            PlayerQueryJob::dispatch($entity, $status->getPlayerList());
        }
    }

    /**
     * Receives the result of a player query job.
     *
     *
     * @return void
     */
    public function processPlayerResult(ServerJobEntity $entity, array $playerIds)
    {
        if ($entity->getIsDryRun()) {
            return;
        }

        foreach ($playerIds as $playerId) {
            $this->serverStatusRepository->store(
                $entity->getServerStatusId(),
                $playerId,
                $entity->getGameIdentifier()
            );
        }
    }
}
