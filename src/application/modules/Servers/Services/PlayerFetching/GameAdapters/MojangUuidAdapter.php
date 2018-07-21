<?php
namespace Application\Modules\Servers\Services\PlayerFetching\GameAdapters;

use Application\Modules\Servers\Services\PlayerFetching\PlayerFetchAdapterInterface;
use Application\Modules\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;
use Application\Modules\Players\Services\MinecraftPlayerLookupService;
use Application\Modules\Players\Models\MinecraftPlayer;

class MojangUuidAdapter implements PlayerFetchAdapterInterface
{

    /**
     * @var MojangApiService
     */
    private $mojangApi;

    /**
     * @var MinecraftPlayerLookupService
     */
    private $userLookupService;


    public function __construct(MojangApiService $mojangApi, MinecraftPlayerLookupService $userLookupService)
    {
        $this->mojangApi = $mojangApi;
        $this->userLookupService = $userLookupService;
    }

    /**
     * {@inheritDoc}
     */
    public function getUniqueIdentifiers(array $aliases = []) : array
    {
        // split names into chunks since the Mojang API
        // won't allow more than 100 names in a batch at once
        $names = collect($aliases)->chunk(100);

        $players = [];
        foreach ($names as $nameChunk) {
            $response = $this->mojangApi->getUuidBatchOf($nameChunk->toArray());
            $players = array_merge($players, $response);
        }

        return $players;
    }

    /**
     * {@inheritDoc}
     */
    public function createPlayers(array $identifiers) : array
    {
        $players = [];
        foreach ($identifiers as $identifier) {
            $players[] = $this->userLookupService->getOrCreateByUuid($identifier->getUuid());
        }
        return $players;
    }
}
