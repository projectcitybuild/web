<?php
namespace App\Services\Queries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\QueryPlayer\PlayerQueryHandler;
use App\Services\Queries\ServerQueryService;
use App\Services\Queries\Entities\ServerJobEntity;

class PlayerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ServerJobEntity
     */
    private $entity;

    /**
     * @var array
     */
    private $playerNamesToQuery;

    /**
     * Create a new job instance.
     *
     * @param integer $serverId
     * @param array $playerNamesToQuery
     */
    public function __construct(ServerJobEntity $entity, array $playerNamesToQuery)
    {
        $this->entity = $entity;
        $this->playerNamesToQuery = $playerNamesToQuery;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlayerQueryHandler $playerQueryHandler, ServerQueryService $serverQueryService)
    {
        $adapter = $this->entity->getPlayerQueryAdapter();
        $playerQueryHandler->setAdapter($adapter);
        $identifiers = $playerQueryHandler->query($this->playerNamesToQuery);

        $serverQueryService->processPlayerResult($this->entity, $identifiers);
    }
}