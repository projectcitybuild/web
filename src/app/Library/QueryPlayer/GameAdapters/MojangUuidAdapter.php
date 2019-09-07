<?php

namespace App\Library\QueryPlayer\GameAdapters;

use App\Library\QueryPlayer\PlayerQueryAdapterContract;
use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Library\Mojang\Api\MojangPlayerApiContract;
use App\Library\Mojang\Models\MojangPlayer;

final class MojangUuidAdapter implements PlayerQueryAdapterContract
{
    /**
     * @var MojangPlayerApiContract
     */
    private $mojangPlayerApi;

    /**
     * @var MinecraftPlayerLookupService
     */
    private $userLookupService;


    public function __construct(
        MojangPlayerApiContract $mojangPlayerApi,
        MinecraftPlayerLookupService $userLookupService
    ) {
        $this->mojangPlayerApi = $mojangPlayerApi;
        $this->userLookupService = $userLookupService;
    }

    /**
     * {@inheritDoc}
     */
    public function getUniqueIdentifiers(array $aliases = []) : array
    {
        // Split names into chunks since the Mojang API
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
