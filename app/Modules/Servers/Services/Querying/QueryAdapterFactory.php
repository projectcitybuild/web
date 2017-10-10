<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\Services\Querying\QueryAdapterInterface;
use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use App\Modules\Servers\GameTypeEnum;
use xPaw\{MinecraftQuery, MinecraftQueryException};

class QueryAdapterFactory {

    /**
     * Instantiates an adapter based on the given game type
     *
     * @param GameTypeEnum $serverGameType
     * @return QueryAdapterInterface
     */
    public function getAdapter(GameTypeEnum $serverGameType) : QueryAdapterInterface {
        switch(strtolower($serverGameType)) {
            case GameTypeEnum::Minecraft:
                $service = new MinecraftQuery();
                return new MinecraftQueryAdapter($service);
            default:
                throw new \Exception('Unsupported game server type');
        }
    }

}