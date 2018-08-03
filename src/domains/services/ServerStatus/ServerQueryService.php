<?php
namespace Domains\Services\ServerStatus;

use Domains\Modules\Servers\Repositories\ServerRepository;
use Domains\Modules\Servers\Repositories\ServerStatusRepository;
use Illuminate\Log\Logger;
use Domains\Library\QueryServer\ServerQueryHandler;
use Domains\Library\QueryServer\ServerQueryAdapterContract;
use Domains\Modules\Servers\GameTypeEnum;
use Domains\Library\QueryServer\ServerQueryResult;


class ServerQueryService
{
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var ServerStatusRepository
     */
    private $serverStatusRepository;

    /**
     * @var ServerQueryHandler
     */
    private $serverQueryHandler;

    /**
     * @var Logger
     */
    private $log;


    public function __construct(ServerRepository $serverRepository,
                                ServerStatusRepository $serverStatusRepository,
                                ServerQueryHandler $serverQueryHandler,
                                Logger $logger) 
    {
        $this->serverRepository = $serverRepository;
        $this->serverStatusRepository = $serverStatusRepository;
        $this->serverQueryHandler = $serverQueryHandler;
        $this->log = $logger;
    }


    public function queryAllServers()
    {
        $servers = $this->serverRepository->getAllQueriableServers();

        foreach ($servers as $server) {
            $gameType = $server->gameType();

            $status = $this->queryForStatus($gameType->serverQueryAdapter(), 
                                            $server->ip, 
                                            $server->port);

            if (count($status->getPlayerList()) > 0) {
                $statusPlayerRepository->store($this->serverStatusId, $playerId, $playerType);

                $this->queryForPlayerIdentifiers($status->getPlayerList(), 
                                                 $gameType->playerQueryAdapter());
            }
        }
    }

    private function queryForStatus(ServerQueryAdapterContract $adapter, string $ip, string $port) : ServerQueryResult
    {
        $time = time();

        $this->serverQueryHandler->setAdapter($serverQueryAdapter);
        $status = $this->serverQueryHandler->queryServer($ip, $port);

        $this->serverStatusRepository->create($server->getKey(),
                                              $status->isOnline(),
                                              $status->getNumOfPlayers(),
                                              $status->getNumOfSlots(),
                                              $time);
        return $status;
    }

    private function queryForPlayerIdentifiers(array $players, PlayerQueryAdapterContract $adapter)
    {
        $playerQueryAdapter = $gameType->playerQueryAdapter();

        $this->playerQueryHandler->setAdapter($playerQueryAdapter);
        $players = $this->playerQueryHandler->queryPlayers($status->getPlayerList());

        foreach ($players as $player) {
            $playerType = $player->getMorphClass();
            $playerId = $player->getKey();

        }
    }
}