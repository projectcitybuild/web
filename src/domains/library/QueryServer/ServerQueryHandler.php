<?php
namespace Domains\Library\QueryServer;

use Domains\Modules\Servers\Repositories\ServerStatusRepository;
use Illuminate\Log\Logger;

class ServerQueryHandler
{
    /**
     * @var ServerQueryAdapterContract
     */
    private $adapter;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var ServerStatusRepository
     */
    private $serverStatusRepository;
    

    public function __construct(ServerStatusRepository $serverStatusRepository,
                                Logger $logger)
    {
        $this->serverStatusRepository = $serverStatusRepository;
        $this->log = $logger;
    }

    /**
     * Sets the adapter that will perform
     * the server query
     *
     * @param ServerQueryAdapterContract $adapter
     * @return void
     */
    public function setAdapter(ServerQueryAdapterContract $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Queries the given server address for
     * its current status and player data
     *
     * @param integer $serverId
     * @param string $ip
     * @param string $port
     * @return ServerQueryResult
     */
    public function queryServer(int $serverId, string $ip, string $port) : ServerQueryResult
    {
        $serverData = [
            'server_id' => $serverId,
            'ip' => $ip,
            'port' => $port,
        ];

        $this->log->notice('Performing server status fetch...', $serverData);
        $start = microtime(true);

        $status = $this->requestStatus($serverId, $ip, $port);
        
        $end = microtime(true) - $start;
        $this->log->notice('Fetch complete ['. ($end / 1000) .'ms]', $serverData);

        return $status;
    }

    private function requestStatus(int $serverId, string $ip, string $port) : ServerQueryResult
    {
        $time = time();

        $status = $this->adapter->query($ip, $port);
        $this->log->info('Received server status', ['status' => $status]);

        $this->serverStatusRepository->create($serverId,
                                              $status->isOnline(),
                                              $status->getNumOfPlayers(),
                                              $status->getNumOfSlots(),
                                              $time);
        return $status;
    }
}