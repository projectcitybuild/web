<?php

namespace Domain\PlayerFetch\Listeners;

use Domain\PlayerFetch\PlayerFetchService;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ServerStatusFetchedListener
{
    private const CACHE_KEY_LAST_KNOWN_PLAYERS = 'player_fetch.last_known_online_players';

    private PlayerFetchService $playerFetchService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PlayerFetchService $playerFetchService)
    {
        $this->playerFetchService = $playerFetchService;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(ServerStatusFetched $event)
    {
        Log::debug('Observed server status fetch');

        if (! $event->result->isOnline || count($event->result->onlinePlayerNames) == 0) {
            return;
        }

        Log::debug('Checking cache for new players');

        $lastKnownOnlinePlayers = Cache::get(self::CACHE_KEY_LAST_KNOWN_PLAYERS, []);

        if (array_values($lastKnownOnlinePlayers) == array_values(($event->result->onlinePlayerNames))) {
            Log::debug('No player changes. Skipping alias fetch');
            return;
        }

        Log::info('Performing player fetch for aliases: '.implode(',', $event->result->onlinePlayerNames));

        $this->playerFetchService->fetch(
            $event->gameType,
            $event->result->onlinePlayerNames,
            $event->fetchTimestamp
        );

        Cache::put(
            self::CACHE_KEY_LAST_KNOWN_PLAYERS,
            $event->result->onlinePlayerNames,
            now()->addHours(1)
        );
    }
}
