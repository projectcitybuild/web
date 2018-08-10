<?php
namespace Domains\Services\Queries;

use Domains\Library\QueryServer\ServerQueryResult;
use Domains\Modules\Servers\GameTypeEnum;
use Domains\Services\Queries\Jobs\ServerQueryJob;
use Domains\Services\Queries\Jobs\PlayerQueryJob;
use Domains\Modules\Servers\Repositories\ServerStatusPlayerRepository;


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
        ServerQueryJob::dispatch($gameType, 
                                 $serverId, 
                                 $ip, 
                                 $port);
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
    public static function processServerResult(int $serverId, ServerQueryResult $status)
    {
        if (count($status->getPlayerList()) > 0) {
            PlayerQueryJob::dispatch($serverId, $status->getPlayerList());
        }
    }

    /**
     * Receives the result of a player query job
     *
     * @param integer $serverId
     * @param array $identifiers
     * @return void
     */
    public static function processPlayerResult(GameTypeEnum $gameType, int $serverId, array $identifiers)
    {
        $serverPlayerRepository = new ServerStatusPlayerRepository();

        foreach ($identifiers as $identifier) {
            $serverPlayerRepository->store($serverId,
                                           $identifier,
                                           $gameType->name());
        }
    }
}