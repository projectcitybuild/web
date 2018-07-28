<?php

namespace Domains\Modules\Servers\Services\PlayerFetching;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Domains\Modules\Servers\Repositories\ServerStatusPlayerRepository;

class PlayerFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $serverStatusId;

    /**
     * @var string
     */
    private $adapter;

    /**
     * @var array
     */
    private $aliases;

    /**
     * Create a new job instance.
     *
     * @param string $playerName
     * @param int $requestTime
     * @return void
     */
    public function __construct(
        int $serverStatusId,
        string $adapter,
        array $aliases
    ) {
        $this->serverStatusId = $serverStatusId;
        $this->adapter = $adapter;
        $this->aliases = $aliases;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ServerStatusPlayerRepository $statusPlayerRepository)
    {
        $adapter = resolve($this->adapter);

        $identifiers = $adapter->getUniqueIdentifiers($this->aliases);
        $players     = $adapter->createPlayers($identifiers);

        foreach ($players as $player) {
            $playerType = $player->getMorphClass();
            $playerId = $player->getKey();

            $statusPlayerRepository->store(
                $this->serverStatusId,
                $playerId,
                $playerType
            );
        }
    }
}
