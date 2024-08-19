<?php

namespace App\Domains\ServerStatus\Jobs;

use App\Domains\ServerStatus\Exceptions\UnsupportedGameException;
use App\Domains\ServerStatus\UseCases\QueryServerStatus;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

final class ServerQueryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Server $server
    ) {
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->server->getKey()))->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     *
     * @throws UnsupportedGameException
     */
    public function handle(QueryServerStatus $queryService)
    {
        $queryService->query(server: $this->server);
    }
}