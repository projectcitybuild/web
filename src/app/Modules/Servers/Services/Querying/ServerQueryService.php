<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Servers\Repositories\ServerStatusRepository;
use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use App\Modules\Servers\Services\Querying\QueryAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\PlayerFetchJob;
use App\Modules\Servers\Models\ServerStatus;
use \Illuminate\Log\Logger;

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
     * @var Logger
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
            $this->query(
                $queryAdapter ?: $this->adapterFactory->getAdapter($server->game_type),
                $server->server_id,
                $server->ip,
                $server->port, 
                $time
            );
        }
    }

    /**
     * Queries a single server for it's status, regardless of
     * its 'is_querying' value
     *
     * @param integer $serverId
     * @return void
     */
    public function queryServer(int $serverId) {
        $server = $this->serverRepository->getById($serverId);

        $this->query(
            $this->adapterFactory->getAdapter($server->game_type),
            $server->server_id,
            $server->ip,
            $server->port
        );
    }

    /**
     * Queries the given server for its status using the
     * given query adapter
     *
     * @param QueryAdapterInterface $queryAdapter
     * @param int $serverId
     * @param string $ip
     * @param string $port
     * @param int|null $time
     * 
     * @return void
     */
    private function query(
        QueryAdapterInterface $queryAdapter,
        int $serverId, 
        string $ip, 
        string $port = null, 
        ?int $time = null
    ) : ServerStatus {

        $response = $queryAdapter->query($ip, $port);

        $status = $this->statusRepository->create(
            $serverId,
            $response->isOnline(),
            $response->getNumOfPlayers(),
            $response->getNumOfSlots(),
            $time ?: time()
        );

        // if players were present on the server, fetch their unique id from
        // an appropriate api
        if($response->getNumOfPlayers() > 0) {
            PlayerFetchJob::dispatch(
                $status->server_status_id,
                $queryAdapter->getPlayerFetchAdapter(),
                $response->getPlayerList()
            );
        }

        return $status;
    }
}