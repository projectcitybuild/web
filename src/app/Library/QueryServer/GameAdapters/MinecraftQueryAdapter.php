<?php
namespace App\Library\QueryServer\GameAdapters;

use App\Library\QueryServer\ServerQueryAdapterContract;
use App\Library\QueryServer\ServerQueryResult;
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
