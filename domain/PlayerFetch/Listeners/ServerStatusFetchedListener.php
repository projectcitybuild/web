<?php

namespace Domain\PlayerFetch\Listeners;

use Domain\PlayerFetch\PlayerFetchService;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Log;

class ServerStatusFetchedListener
{
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
     * @param ServerStatusFetched $event
     * @return void
     */
    public function handle(ServerStatusFetched $event)
    {
        Log::debug('Observed server status fetch');

        if (!$event->result->isOnline || count($event->result->onlinePlayerNames) == 0) {
            return;
        }
        Log::info('Performing player fetch for aliases: '.implode(',', $event->result->onlinePlayerNames));

        $this->playerFetchService->fetch(
            $event->gameType,
            $event->result->onlinePlayerNames,
            $event->fetchTimestamp
        );
    }
}
