<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use App\Modules\Servers\Models\Server;
use App\Modules\Users\Jobs\FetchIdentifierJob;
use \Illuminate\Log\Writer as Logger;

class ServerQueryService {
 
    private $serverRepository;
    private $statusRepository;
    private $adapterFactory;
    private $logger;

    public function __construct(ServerRepository $serverRepository, 
                                ServerStatusRepository $statusRepository, 
                                QueryAdapterFactory $adapterFactory,
                                Logger $logger) 
                                {
        $this->serverRepository = $serverRepository;
        $this->statusRepository = $statusRepository;
        $this->adapterFactory = $adapterFactory;
        $this->logger = $logger;
    }

    /**
     * Queries every server that is set as 'is_querying'
     *
     * @return void
     */
    public function queryAllServers() {
        $servers = $this->serverRepository->getAllQueriableServers();
        $time = time();

        foreach($servers as $server) {
            $this->queryServer($server, $time);
        }
    }

    /**
     * Queries the given server for its status
     *
     * @param string $ip
     * @param string $port
     * @param int $time
     * @return void
     */
    public function queryServer(Server $server, $time = null) {
        $requestTime = time();
        
        $adapter = $this->adapterFactory->getAdapter($server->game_type);
        $status = $adapter->query(
            $server->ip,
            $server->port
        );

        if($status->hasException()) {
            $this->logger->info(
                'Server query ['.$server->getAddress().'] returned response: '.$status->getException()->getMessage()
            );
        }

        $this->statusRepository->create(
            $server->server_id,
            $status->isOnline(),
            $status->getNumOfPlayers(),
            $status->getNumOfSlots(),
            $status->getPlayerList(),
            $time ?: time()
        );

        
        foreach($status->getPlayerList() as $player) {
            $adapter->createGameUser($player, $requestTime);
        }

        $this->logger->info('Queried server ['. $server->name .']: ' . $server->getAddress());
    }
}