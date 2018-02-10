<?php
namespace App\Modules\Servers\Services\PlayerFetching\GameAdapters;

use App\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;
use App\Modules\Players\Services\MinecraftPlayerLookupService;
use App\Modules\Players\Models\MinecraftPlayer;

class MojangUuidAdapter implements PlayerFetchAdapterInterface {

    /**
     * @var MojangApiService
     */
    private $mojangApi;

    /**
     * @var MinecraftPlayerLookupService
     */
    private $userLookupService;


    public function __construct(MojangApiService $mojangApi, MinecraftPlayerLookupService $userLookupService) {
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

        $players = [];
        foreach($names as $nameChunk) {

            // TODO: move this into a job
            $apiResponse = $this->mojangApi->getUuidBatchOf($nameChunk->toArray());

            foreach($apiResponse as $alias => $player) {
                $uuid = $player->getUuid();
                $player = $this->userLookupService->getOrCreateByUuid($uuid);
                
                $players[$player->player_minecraft_id] = MinecraftPlayer::class;
            }
        }

        return $players;
    }

}