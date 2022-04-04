<?php

namespace Domain\PlayerFetch;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\GameType;
use Domain\PlayerFetch\Jobs\PlayerFetchJob;
use Domain\PlayerFetch\Repositories\PlayerFetchRepository;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Log;

final class PlayerFetchService
{
    private PlayerFetchRepository $playerRepository;
    private PlayerFetchAdapterFactoryContract $adapterFactory;

    public function __construct(
        PlayerFetchRepository $playerRepository,
        PlayerFetchAdapterFactoryContract $adapterFactory
    ) {
        $this->playerRepository = $playerRepository;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * Fetches the UUID for every given alias.
     *
     * This operation will block the current process until the query succeeds or fails.
     *
     * @return \Library\Mojang\Models\MojangPlayer[]
     *
     * @throws UnsupportedGameException
     */
    public function fetchSynchronously(GameType $gameType, array $aliases, ?int $timestamp = null): array
    {
        // FIXME: this service only supports Minecraft

        $adapter = $this->adapterFactory->make($gameType);
        $players = $adapter->fetch($aliases, $timestamp);

        foreach ($players as $player) {
            $existingPlayer = MinecraftPlayer::where('uuid', $player->getUuid())->first();

            if (! $existingPlayer) {
                $this->playerRepository->createPlayerWithAlias(
                    $player->getUuid(),
                    $player->getAlias()
                );
                continue;
            }

            // Add a new alias for the player if they've changed their name
            // since our last check
            $aliases = $existingPlayer->aliases;
            if (count($aliases) > 0 && $aliases[0]->alias === $player->getAlias()) {
                continue;
            }
            $this->playerRepository->addAliasForPlayer(
                $existingPlayer->getKey(),
                $player->getAlias()
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
}
