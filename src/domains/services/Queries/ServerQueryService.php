<?php
namespace Domains\Services\Queries;

use Domains\Library\QueryServer\ServerQueryResult;
use Domains\Modules\Servers\GameTypeEnum;
use Domains\Services\Queries\Jobs\ServerQueryJob;
use Domains\Services\Queries\Jobs\PlayerQueryJob;


class ServerQueryService
{
    public function dispatchQuery(GameTypeEnum $gameType, int $serverId, string $ip, string $port)
    {
        ServerQueryJob::dispatch($gameType, 
                                 $serverId, 
                                 $ip, 
                                 $port);
    }

    public static function processServerResult(ServerQueryResult $status)
    {
        if (count($status->getPlayerList()) > 0) {
            // PlayerQueryJob::dispatch($status->getPlayerList());
            // foreach ($status->getPlayerList() as $player) {
            //     $this->serverStatusPlayerRepository->store($serverId, 
            //                                                $playerId, 
            //                                                $playerType);
            // }
            // $this->fetchPlayerIdentifiers($status->getPlayerList());
        }
    }
}