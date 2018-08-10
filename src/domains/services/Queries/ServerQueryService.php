<?php
namespace Domains\Services\Queries;

use Domains\Library\QueryServer\ServerQueryResult;
use Domains\GameTypeEnum;
use Domains\Services\Queries\Jobs\ServerQueryJob;
use Domains\Services\Queries\Jobs\PlayerQueryJob;
use Domains\Modules\Servers\Repositories\ServerStatusPlayerRepository;
use Domains\Services\Queries\Entities\ServerJobEntity;


class ServerQueryService
{
    /**
     * Dispatches a job to query a server for its
     * current status and player list
     *
     * @param GameTypeEnum $gameType
     * @param integer $serverId
     * @param string $ip
     * @param string $port
     * @return void
     */
    public function dispatchQuery(GameTypeEnum $gameType, int $serverId, string $ip, string $port)
    {
        $entity = new ServerJobEntity($gameType->serverQueryAdapter(),
                                      $gameType->playerQueryAdapter(),
                                      $gameType->name(),
                                      $serverId,
                                      $ip,
                                      $port);

        ServerQueryJob::dispatch($entity);
    }

    /**
     * Receives the result of a server query job, then
     * dispatches a player query job if players were online,
     * to uniquely identify each player and store them in
     * PCB's player database for statistics
     *
     * @param integer $serverId
     * @param ServerQueryResult $status
     * @return void
     */
    public static function processServerResult(ServerJobEntity $entity, ServerQueryResult $status)
    {
        if (count($status->getPlayerList()) > 0) {
            PlayerQueryJob::dispatch($entity, $status->getPlayerList());
        }
    }

    /**
     * Receives the result of a player query job
     *
     * @param integer $serverId
     * @param array $playerIds
     * @return void
     */
    public static function processPlayerResult(ServerJobEntity $entity, array $playerIds)
    {
        $serverPlayerRepository = new ServerStatusPlayerRepository();

        foreach ($playerIds as $playerId) {
            $serverPlayerRepository->store($entity->getServerId(),
                                           $playerId,
                                           $entity->getGameIdentifier());
        }
    }
}