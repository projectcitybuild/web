<?php

namespace App\Services\Queries\Jobs;

use App\Library\QueryPlayer\PlayerQueryHandler;
use App\Services\Queries\Entities\ServerJobEntity;
use App\Services\Queries\ServerQueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PlayerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ServerJobEntity $entity;

    /**
     * @var array
     */
    private array $playerNamesToQuery;

    /**
     * Create a new job instance.
     *
     * @param int $serverId
     * @param array $playerNamesToQuery
     */
    public function __construct(ServerJobEntity $entity, array $playerNamesToQuery)
    {
        $this->entity = $entity;
        $this->playerNamesToQuery = $playerNamesToQuery;
    }

    /**
     * Execute the job.
     */
    public function handle(PlayerQueryHandler $playerQueryHandler, ServerQueryService $serverQueryService): void
    {
        $adapter = $this->entity->getPlayerQueryAdapter();
        $playerQueryHandler->setAdapter($adapter);
        $identifiers = $playerQueryHandler->query($this->playerNamesToQuery);

        $serverQueryService->processPlayerResult($this->entity, $identifiers);
    }
}
