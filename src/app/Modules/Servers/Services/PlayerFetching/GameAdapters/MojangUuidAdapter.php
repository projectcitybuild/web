<?php
namespace App\Modules\Servers\Services\PlayerFetching\GameAdapters;

use App\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;
use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\UserAliasTypeEnum;

class MojangUuidAdapter implements PlayerFetchAdapterInterface {

    /**
     * @var MojangApiService
     */
    private $mojangApi;

    /**
     * @var GameUserLookupService
     */
    private $userLookupService;


    public function __construct(MojangApiService $mojangApi, GameUserLookupService $userLookupService) {
        $this->mojangApi = $mojangApi;
        $this->userLookupService = $userLookupService;
    }

    /**
     * Returns the uuid of the given players
     *
     * @param array $key
     * @return array
     */
    public function getUniqueIdentifiers(array $key = []) : array {
        // split names into chunks since the Mojang API
        // won't allow more than 100 names in a batch at once 
        $names = collect($key)->chunk(100);

        $gameUserIds = [];
        foreach($names as $nameChunk) {

            // TODO: move this into a job
            $apiResponse = $this->mojangApi->getUuidBatchOf($nameChunk->toArray());

            foreach($apiResponse as $alias => $player) {
                $uuid = $player->getUuid();
                $gameUser = $this->userLookupService->getOrCreateGameUser(UserAliasTypeEnum::MINECRAFT_UUID, $uuid);
                $gameUserIds[] = $gameUser->game_user_id;                              
            }
        }

        return $gameUserIds;
    }

}