<?php

namespace Domain\ServerStatus\Jobs;

use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\ServerQueryService;
use Entities\Models\Eloquent\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ServerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Server $server
    ) {}

    /**
     * Execute the job.
     *
     * @throws UnsupportedGameException
     */
    public function handle(ServerQueryService $queryService)
    {
        $queryService->query(server: $this->server);
    }
}
