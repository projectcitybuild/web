<?php

namespace App\Domains\HealthCheck\Jobs;

use App\Core\Data\JobQueue;
use App\Domains\HealthCheck\Data\QueueHealthCheck;
use App\Domains\HealthCheck\HealthCheckReporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class QueueWorkerHeartbeatJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue(JobQueue::DEFAULT->value);
    }

    public function handle(): void
    {
        $healthCheck = new QueueHealthCheck;

        if (! $healthCheck->enabled()) {
            return;
        }

        (new HealthCheckReporter($healthCheck))->success();
    }
}
