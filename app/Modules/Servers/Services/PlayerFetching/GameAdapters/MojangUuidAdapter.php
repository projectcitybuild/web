<?php
namespace App\Modules\Servers\Services\PlayerFetching\GameAdapters;

use App\Modules\Servers\Services\PlayerFetching\PlayerFetcherInterface;
use App\Modules\Servers\Services\Mojang\MojangApiService;

class MojangUuidAdapter implements PlayerFetcherInterface {

    /**
     * @var MojangApiService
     */
    private $ApiService;

    public function __construct(MojangApiService $apiService) {
        $this->apiService = $apiService;
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
            $apiResponse = $this->apiService->getUuidBatchOf($nameChunk);

            foreach($apiResponse as $alias => $player) {
                $uuid = $player->getUuid();
                
            }
        }
    }

}