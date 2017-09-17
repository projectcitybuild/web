<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use Illuminate\Support\Facades\Log as Logger;

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
                $time
            );
        }
    }

}