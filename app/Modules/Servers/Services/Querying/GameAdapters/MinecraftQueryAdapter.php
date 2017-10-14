<?php
namespace App\Modules\Servers\Services\Querying\GameAdapters;

use App\Modules\Servers\Services\Querying\{QueryAdapterInterface, QueryResult};
use App\Modules\Servers\Services\Mojang\UuidFetcher;
use App\Modules\Users\Services\GameUserLookupService;
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

    /**
     * @var GameUserLookupService
     */
    private $gameUserLookup;


    public function __construct(MinecraftQuery $queryService, UuidFetcher $uuidFetcher, GameUserLookupService $gameUserLookup) {
        $this->queryService = $queryService;
        $this->uuidFetcher = $uuidFetcher;
        $this->gameUserLookup = $gameUserLookup;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = null) : QueryResult {
        $port = $port ?: 25565;

        if(empty($ip)) {
            throw new \Exception('Invalid server IP provided');
        }

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
    public function fetchPlayerIdentifier($player) {
        $requestTime = time();

        /**
         * Returns a promise that will fetch the given player alias's uuid from Mojang, 
         * at the time of the server status query. Additionally checks if the uuid
         * has an associated PCB game account and creates one if necessary.
         */
        return function() use($player, $requestTime) {
            $uuid = $this->uuidFetcher->getUuidOf($player, $requestTime);
            
            // if no uuid returned, the Mojang server is probably down
            if(!$uuid) {
                throw new \Exception('UUID fetch response is empty. Is the Mojang server down?');
            }

            $gameUser = $this->gameUserLookup->getOrCreateGameUserId('MINECRAFT_UUID', $uuid->getUuid());
            
        };
    }

}