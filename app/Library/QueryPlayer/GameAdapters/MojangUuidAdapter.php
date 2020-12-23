<?php

namespace App\Library\QueryPlayer\GameAdapters;

use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayer;
use App\Library\QueryPlayer\PlayerQueryAdapterContract;

final class MojangUuidAdapter implements PlayerQueryAdapterContract
{
    private MojangPlayerApi $mojangPlayerApi;

    private MinecraftPlayerLookupService $userLookupService;

    public function __construct(
        MojangPlayerApi $mojangPlayerApi,
        MinecraftPlayerLookupService $userLookupService
    ) {
        $this->mojangPlayerApi = $mojangPlayerApi;
        $this->userLookupService = $userLookupService;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueIdentifiers(array $aliases = []): array
    {
        // Split names into chunks since the Mojang API
        // won't allow more than 10 names in a batch at once
        $names = collect($aliases)->chunk(10);

        $players = [];
        foreach ($names as $nameChunk) {
            $response = $this->mojangPlayerApi->getUuidBatchOf($nameChunk->toArray());
            $response = array_map(function (MojangPlayer $player) {
                $uuid = $player->getUuid();
                return str_replace('-', '', $uuid);
            }, $response);

            $players = array_merge($players, $response);
        }

        return $players;
    }

    /**
     * {@inheritdoc}
     */
    public function createPlayers(array $identifiers): array
    {
        $players = [];
        foreach ($identifiers as $identifier) {
            $player = $this->userLookupService->getOrCreateByUuid($identifier);
            $players[] = $player->getKey();
        }
        return $players;
    }
}
