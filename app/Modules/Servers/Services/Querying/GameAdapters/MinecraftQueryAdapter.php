<?php
namespace App\Modules\Servers\Services\Querying\GameAdapters;

use App\Modules\Servers\Services\Querying\{QueryAdapterInterface, QueryResult};
use App\Modules\Servers\Services\Mojang\UuidFetcher;
use App\Modules\Servers\Services\Querying\Jobs\CreateMinecraftPlayerJob;
use xPaw\{MinecraftQuery, MinecraftQueryException};

class MinecraftQueryAdapter implements QueryAdapterInterface {

    /**
     * @var MinecraftQuery
     */
    private $queryService;

    /**
     * @var UuidFetcher
     */
    private $uuidFetcher;

    public function __construct(MinecraftQuery $queryService, UuidFetcher $uuidFetcher) {
        $this->queryService = $queryService;
        $this->uuidFetcher = $uuidFetcher;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = null) : QueryResult {
        if(empty($ip)) {
            throw new \Exception('Invalid server IP provided');
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
        }
        catch(MinecraftQueryException $e) {
            $response = new QueryResult(false, 0, 0, []);
            $response->setException($e);

            return $response;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createGameUser(string $player, int $requestTime) {
        return CreateMinecraftPlayerJob::dispatch($player, $requestTime)
            ->onQueue('game_identifier_lookup');
    }

}