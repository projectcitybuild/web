<?php

namespace Domain\PlayerFetch;

use App;
use App\Entities\GameType;
use App\Entities\Players\Models\MinecraftPlayer;
use Domain\PlayerFetch\Adapters\MojangUUIDFetchAdapter;
use Domain\PlayerFetch\Jobs\PlayerFetchJob;
use Domain\PlayerFetch\Repositories\PlayerFetchRepository;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Log;

final class PlayerFetchService
{
    private PlayerFetchRepository $playerRepository;

    public function __construct(PlayerFetchRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * Fetches the UUID for every given alias
     *
     * This operation will block the current process until the query succeeds or fails.
     *
     * @return MinecraftPlayer[]
     * @throws UnsupportedGameException
     */
    public function fetchSynchronously(GameType $gameType, array $aliases, ?int $timestamp = null): array
    {
        // FIXME: this service only supports Minecraft

        $adapter = $this->getAdapter($gameType);
        $players = $adapter->fetch($aliases, $timestamp);

        foreach ($players as $player) {
            $this->playerRepository->createPlayerIfNotExist(
                $player->getAlias(),
                $player->getUuid()
            );
        }

        Log::debug('Player data fetch completed');

        return $players;
    }

    /**
     * Fetches the UUID for every given alias.
     *
     * This operation is non-blocking, and queues a job to perform the operation
     * eventually in the future.
     */
    public function fetch(GameType $gameType, array $aliases, ?int $timestamp = null)
    {
        PlayerFetchJob::dispatch($gameType, $aliases, $timestamp);
    }

    private function getAdapter(GameType $gameType): PlayerFetchAdapter
    {
        switch ($gameType->valueOf()) {
            case GameType::Minecraft:
                return App::make(MojangUUIDFetchAdapter::class);
            default:
                throw new UnsupportedGameException($gameType->name()." is not supported");
        }
    }
}
