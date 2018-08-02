<?php
namespace Domains\Library\ServerQuery\GameAdapters;

use Domains\Library\ServerQuery\ServerQueryAdapterContract;
use Domains\Library\ServerQuery\ServerQueryResult;
use Domains\Modules\Servers\Services\PlayerFetching\GameAdapters\MojangUuidAdapter;
use Domains\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

class MinecraftQueryAdapter implements ServerQueryAdapterContract
{
    /**
     * @var MinecraftQuery
     */
    private $queryService;


    public function __construct(MinecraftQuery $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $ip, $port = 25565) : ServerQueryResult
    {
        try {
            $this->queryService->Connect($ip, $port);
            
            $info = $this->queryService->GetInfo() ?: [];
            $players = $this->queryService->GetPlayers() ?: [];

            $numPlayers = array_key_exists('Players', $info) ? $info['Players'] : -1;
            $numSlots   = array_key_exists('MaxPlayers', $info) ? $info['MaxPlayers'] : -1;

            return new ServerQueryResult(true,
                                         $numPlayers,
                                         $numSlots,
                                         $players);

        } catch (MinecraftQueryException $e) {
            return new ServerQueryResult();
        }
    }
}
