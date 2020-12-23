<?php

namespace App\Services\Queries\Jobs;

use App\Library\QueryServer\ServerQueryHandler;
use App\Services\Queries\Entities\ServerJobEntity;
use App\Services\Queries\ServerQueryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ServerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ServerJobEntity $entity;

    /**
     * Create a new job instance.
     */
    public function __construct(ServerJobEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     */
    public function handle(ServerQueryHandler $serverQueryHandler, ServerQueryService $serverQueryService): void
    {
        $serverQueryHandler->setAdapter($this->entity->getServerQueryAdapter());

        $status = $serverQueryHandler->queryServer(
            $this->entity->getServerId(),
            $this->entity->getIp(),
            $this->entity->getPort()
        );

        $lastCreatedId = $serverQueryHandler->getLastCreatedId();

        $this->entity->setServerStatusId($lastCreatedId);

        $serverQueryService->processServerResult($this->entity, $status);
    }
}
