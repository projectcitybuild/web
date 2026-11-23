<?php

namespace App\Core\Domains\Scheduling\Jobs;

use App\Domains\BackgroundJobs\ScheduledTaskRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class RunScheduledTask implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $taskId,
        private string $taskName,
    ) {}

    public function displayName(): string
    {
        return $this->taskName;
    }

    public function handle(ScheduledTaskRegistry $scheduledTasks): void
    {
        $scheduledTasks->run($this->taskId);
    }
}
