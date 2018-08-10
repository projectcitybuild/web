<?php
namespace Domains\Services\Queries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Domains\Library\QueryPlayer\PlayerQueryHandler;
use Domains\Services\Queries\ServerQueryService;
use Domains\Services\Queries\Entities\ServerJobEntity;

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
    public function handle(PlayerQueryHandler $playerQueryHandler)
    {
        $playerQueryHandler->setAdapter($this->entity->getPlayerQueryAdapter());
        $identifiers = $playerQueryHandler->query($this->playerNamesToQuery);

        ServerQueryService::processPlayerResult($this->entity, $identifiers);
    }
}