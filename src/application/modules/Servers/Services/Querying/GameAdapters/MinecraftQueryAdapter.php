<?php
namespace Application\Modules\Servers\Services\Querying\GameAdapters;

use Application\Modules\Servers\Services\Querying\QueryAdapterInterface;
use Application\Modules\Servers\Services\Querying\QueryResult;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use Application\Modules\Servers\Services\PlayerFetching\GameAdapters\MojangUuidAdapter;
use Application\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;

class MinecraftQueryAdapter implements QueryAdapterInterface
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
    public function query(string $ip, $port = null) : QueryResult
    {
        $port = $port ?: 25565;

        try {
            $this->queryService->Connect($ip, $port);
            
            $info = $this->queryService->GetInfo() ?: [];
            $players = $this->queryService->GetPlayers() ?: [];

            $numPlayers = array_key_exists('Players', $info) ? $info['Players'] : -1;
            $numSlots   = array_key_exists('MaxPlayers', $info) ? $info['MaxPlayers'] : -1;

            return new QueryResult(
                true,
                $numPlayers,
                $numSlots,
                $players
            );
        } catch (MinecraftQueryException $e) {
            return new QueryResult();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPlayerFetchAdapter() : string
    {
        return MojangUuidAdapter::class;
    }
}
