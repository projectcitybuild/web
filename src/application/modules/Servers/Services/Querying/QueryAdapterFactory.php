<?php
namespace Application\Modules\Servers\Services\Querying;

use Application\Modules\Servers\Services\Querying\QueryAdapterInterface;
use Application\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use Application\Modules\Servers\GameTypeEnum;

class QueryAdapterFactory
{

    /**
     * Instantiates an adapter based on the given game type
     *
     * @param int $gameType
     *
     * @return QueryAdapterInterface
     * @throws \Exception
     */
    public function getAdapter(int $gameType) : QueryAdapterInterface
    {
        switch (strtolower($gameType)) {
            case GameTypeEnum::Minecraft:
                return resolve(MinecraftQueryAdapter::class);
                
            default:
                throw new \Exception('Unsupported game server type');
        }
    }
}
