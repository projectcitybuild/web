<?php
namespace App\Modules\Servers\Services\PlayerFetching\GameAdapters;

use App\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;
use App\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;

class MojangUuidAdapter implements PlayerFetchAdapterInterface {

    /**
     * @var MojangApiService
     */
    private $mojangApi;

    public function __construct(MojangApiService $mojangApi) {
        $this->mojangApi = $mojangApi;
    }

    /**
     * Returns the uuid of the given players
     *
     * @param array $key
     * @return array
     */
    public function getUniqueIdentifiers(array $key = []) : array {
        $names = collect($key)->chunk(100);
        foreach($names as $nameChunk) {
            $apiResponse = $this->mojangApi->getUuidBatchOf($nameChunk);

            foreach($apiResponse as $alias => $player) {
                $uuid = $player->getUuid();
                
            }
        }
    }

}