<?php
namespace App\Modules\Servers\Services\Querying;

use App\Modules\Servers\GameTypeEnum;
use App\Modules\Servers\Services\PlayerFetching\PlayerFetcherInterface;
use App\Modules\Servers\Services\PlayerFetching\GameAdapters\MojangUuidAdapter;

class PlayerFetcherAdapterFactory {

    /**
     * Instntiates an adapter based on the given game type
     *
     * @param int $gameType
     * 
     * @return PlayerFetcherInterface
     * @throws \Exception
     */
    public function getAdapter(int $gameType) : PlayerFetcherInterface {
        switch(strtolower($gameType)) {
            case GameTypeEnum::Minecraft:
                return resolve(MojangUuidAdapter::class);
                
            default:
                throw new \Exception('Unsupported game server type');
        }
    }

}