<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use \Illuminate\Log\Logger;
use App\Modules\Servers\Services\Querying\QueryAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\PlayerFetcherInterface;
use App\Modules\Servers\Models\ServerStatus;
use App\Modules\Servers\Repositories\ServerStatusPlayerRepository;

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
     * @var ServerStatusPlayerRepository
     */
    private $statusPlayerRepository;

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
        ServerStatusPlayerRepository $statusPlayerRepository,
        QueryAdapterFactory $adapterFactory,
        Logger $logger
    ) {
        $this->serverRepository = $serverRepository;
        $this->statusRepository = $statusRepository;
        $this->statusPlayerRepository = $statusPlayerRepository;
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
        //
        // NOTE: each adapter should implement its own job queuing where required
        // as long running operations will delay querying the next server in-line
        if($response->getNumOfPlayers() > 0) {
            $playerFetcher = $queryAdapter->getPlayerFetchAdapter();
            $playerIds = $playerFetcher->getUniqueIdentifiers($response->getPlayerList());
            
            foreach($playerIds as $id => $type) {
                $this->statusPlayerRepository->store($status->server_status_id, $id, $type);
            }
        }

        return $status;
    }
}