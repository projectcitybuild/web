<?php
namespace App\Services\Queries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\QueryServer\ServerQueryHandler;
use App\Services\Queries\ServerQueryService;
use App\Services\Queries\Entities\ServerJobEntity;

class ServerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ServerJobEntity
     */
    private $entity;
    
    /**
     * Create a new job instance.
     *
     * @param ServerJobEntity $entity
     */
    public function __construct(ServerJobEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @param ServerQueryHandler $serverQueryHandler
     * @return void
     */
    public function handle(ServerQueryHandler $serverQueryHandler)
    {
        $serverQueryHandler->setAdapter($this->entity->getServerQueryAdapter());
        $status = $serverQueryHandler->queryServer($this->entity->getServerId(), 
                                                   $this->entity->getIp(), 
                                                   $this->entity->getPort());

        $lastCreatedId = $serverQueryHandler->getLastCreatedId();
        $this->entity->setServerStatusId($lastCreatedId);

        ServerQueryService::processServerResult($this->entity, $status);
    }
}