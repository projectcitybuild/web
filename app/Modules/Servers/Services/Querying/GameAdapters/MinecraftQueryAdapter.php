<?php
namespace App\Modules\Servers\Services\Querying\GameAdapters;

use App\Modules\Servers\Services\Querying\{QueryAdapterInterface, QueryResult};
use xPaw\{MinecraftQuery, MinecraftQueryException};

class MinecraftQueryAdapter implements QueryAdapterInterface {

    /**
     * @var MinecraftQuery
     */
    private $queryService;

    public function __construct(MinecraftQuery $queryService) {
        $this->queryService = $queryService;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = null) : QueryResult {
        if(empty($ip)) {
            throw new \Exception('No server IP provided');
        }
        $port = $port ?: 25565;

        try {
            $this->queryService->Connect($ip, $port);
            
            $info = $this->queryService->GetInfo();
            $players = $this->queryService->GetPlayers();

            return new QueryResult(
                true,
                $info['Players'],
                $info['MaxPlayers'],
                $players ?: []
            );
        
        } catch(MinecraftQueryException $e) {
            $response = new QueryResult(false, 0, 0, []);
            $response->setException($e);

            return $response;
        }
    }

}