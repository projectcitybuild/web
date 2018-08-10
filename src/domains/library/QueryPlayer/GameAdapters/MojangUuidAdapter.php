<?php
namespace Domains\Library\QueryPlayer\GameAdapters;

use Domains\Library\QueryPlayer\PlayerQueryAdapterContract;
use Domains\Modules\Players\Services\MinecraftPlayerLookupService;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Domains\Library\Mojang\Api\MojangPlayerApiThrottled;
use Domains\Library\Mojang\Models\MojangPlayer;

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
            $response = array_map(function (MojangPlayer $player) {
                return $player->getUuid();
            }, $response);

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
            $player = $this->userLookupService->getOrCreateByUuid($identifier);
            $players[] = $player->getKey();
        }
        return $players;
    }
}
