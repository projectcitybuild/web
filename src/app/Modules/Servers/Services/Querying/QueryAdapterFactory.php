<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Services\Querying\QueryAdapterInterface;
use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use App\Modules\Servers\GameTypeEnum;

class QueryAdapterFactory {

    /**
     * Instantiates an adapter based on the given game type
     *
     * @param int $gameType
     * 
     * @return QueryAdapterInterface
     * @throws \Exception
     */
    public function getAdapter(int $gameType) : QueryAdapterInterface {
        switch(strtolower($gameType)) {
            case GameTypeEnum::Minecraft:
                return resolve(MinecraftQueryAdapter::class);
                
            default:
                throw new \Exception('Unsupported game server type');
        }
    }

}