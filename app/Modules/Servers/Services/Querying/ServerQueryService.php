<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use \Illuminate\Log\Writer as Logger;
use App\Modules\Servers\Services\Querying\QueryAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\PlayerFetcherInterface;
use App\Modules\Servers\Models\ServerStatus;

class ServerQueryService {
 
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var ServerStatusRepository
     */
    private $statusRepository;

    /**
     * @var QueryAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Illuminate\Log\Writer
     */
    private $logger;

    public function __construct(
        ServerRepository $serverRepository, 
        ServerStatusRepository $statusRepository, 
        QueryAdapterFactory $adapterFactory,
        Logger $logger
    ) {
        $this->serverRepository = $serverRepository;
        $this->statusRepository = $statusRepository;
        $this->adapterFactory = $adapterFactory;
        $this->logger = $logger;
    }

    /**
     * Queries every server that is set as 'is_querying'
     *
     * @param QueryAdapterInterface $queryAdapter
     * 
     * @return void
     */
    public function queryAllServers(QueryAdapterInterface $queryAdapter = null) {
        $servers = $this->serverRepository->getAllQueriableServers();
        $time = time();

        foreach($servers as $server) {
            $this->queryServer(
                $queryAdapter ?: $this->adapterFactory->getAdapter($server->game_type),
                $server->server_id,
                $server->ip,
                $server->port, 
                $time
            );
        }
    }

    /**
     * Queries the given server for its status
     *
     * @param QueryAdapterInterface $queryAdapter
     * @param int $serverId
     * @param string $ip
     * @param string $port
     * @param int $time
     * @return void
     */
    public function queryServer(
        QueryAdapterInterface $queryAdapter,
        int $serverId, 
        string $ip, 
        string $port = null, 
        $time = null
    ) : ServerStatus {

        $status = $queryAdapter->query($ip, $port);

        if($status->hasException()) {
            $this->logger->info('Server query ['.$ip.'] returned response: '.$status->getException()->getMessage());
        }

        return $this->statusRepository->create(
            $serverId,
            $status->isOnline(),
            $status->getNumOfPlayers(),
            $status->getNumOfSlots(),
            $status->getPlayerList(),
            $time ?: time()
        );
    }
}