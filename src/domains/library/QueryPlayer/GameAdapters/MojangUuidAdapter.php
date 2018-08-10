<?php
namespace Domains\Modules\Servers\Services\PlayerFetching\GameAdapters;

use Domains\Library\QueryPlayer\PlayerQueryAdapterContract;
use Domains\Modules\Players\Services\MinecraftPlayerLookupService;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Domains\Library\Mojang\Api\MojangPlayerApiThrottled;

class MojangUuidAdapter implements PlayerQueryAdapterContract
{

    /**
     * @var MojangPlayerApiThrottled
     */
    private $mojangPlayerApi;

    /**
     * @var MinecraftPlayerLookupService
     */
    private $userLookupService;


    public function __construct(MojangPlayerApiThrottled $mojangPlayerApi,
                                MinecraftPlayerLookupService $userLookupService)
    {
        $this->mojangPlayerApi = $mojangPlayerApi;
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
            $response = $this->mojangPlayerApi->getUuidBatchOf($nameChunk->toArray());
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
