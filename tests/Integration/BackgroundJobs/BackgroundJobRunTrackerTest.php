<?php

use App\Core\Data\JobQueue;
use App\Core\Domains\Scheduling\Jobs\RunScheduledTask;
use App\Domains\BackgroundJobs\BackgroundJobRunTracker;
use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Models\BackgroundJobRun;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

it('tracks a synchronous queued job through successful completion', function () {
    BackgroundJobTrackingTestJob::$handled = false;

    Queue::push(new BackgroundJobTrackingTestJob);

    expect(BackgroundJobTrackingTestJob::$handled)->toBeTrue();
    $this->assertDatabaseCount('background_job_runs', 1);

    $run = BackgroundJobRun::query()->sole();

    expect($run->status)->toBe(BackgroundJobRunStatus::SUCCEEDED)
        ->and($run->job_type)->toBe('other')
        ->and($run->attempts)->toBe(1)
        ->and($run->queue)->toBe(JobQueue::DEFAULT->value)
        ->and($run->completed_at)->not->toBeNull()
        ->and($run->duration_ms)->not->toBeNull()
        ->and($run->metadata)->toBeNull();
});

it('uses a queued scheduled task display name for recent activity', function () {
    $job = new RunScheduledTask('scheduled-task-id', 'Recover player mail broadcasts');
    $payload = app(BackgroundJobRunTracker::class)->creatingPayload('database', 'default', [
        'displayName' => $job->displayName(),
        'data' => ['command' => $job],
    ]);

    $run = BackgroundJobRun::query()->findOrFail($payload['background_job_run_id']);

    expect($run->job_name)->toBe('Recover player mail broadcasts');
});

it('stores the logical queue name instead of the Redis queue key', function () {
    $job = new BackgroundJobTrackingTestJob;
    $payload = app(BackgroundJobRunTracker::class)->creatingPayload('redis', 'queues:player-mail', [
        'displayName' => BackgroundJobTrackingTestJob::class,
        'data' => ['command' => $job],
    ]);

    $run = BackgroundJobRun::query()->findOrFail($payload['background_job_run_id']);

    expect($run->queue)->toBe(JobQueue::PLAYER_MAIL->value);
});

it('updates the same tracking run when a queued job is retried', function () {
    $payload = app(BackgroundJobRunTracker::class)->creatingPayload('database', 'default', [
        'displayName' => BackgroundJobTrackingTestJob::class,
        'maxTries' => 3,
        'data' => ['command' => new BackgroundJobTrackingTestJob],
    ]);
    $trackingId = $payload['background_job_run_id'];
    $eventPayload = [
        'background_job_run_id' => $trackingId,
        'maxTries' => 3,
        'uuid' => (string) Str::uuid(),
    ];
    $firstAttempt = new BackgroundJobLifecycleTestJob($eventPayload, 1, 'database-job-1');
    $secondAttempt = new BackgroundJobLifecycleTestJob($eventPayload, 2, 'database-job-1');

    Event::dispatch(new JobProcessing('database', $firstAttempt));
    Event::dispatch(new JobExceptionOccurred('database', $firstAttempt, new RuntimeException('temporary failure')));
    Event::dispatch(new JobProcessing('database', $secondAttempt));
    Event::dispatch(new JobProcessed('database', $secondAttempt, 15));

    $run = BackgroundJobRun::query()->findOrFail($trackingId);

    expect(BackgroundJobRun::query()->count())->toBe(1)
        ->and($run->status)->toBe(BackgroundJobRunStatus::SUCCEEDED)
        ->and($run->attempts)->toBe(2)
        ->and($run->queue_job_id)->toBe('database-job-1');
});

it('records terminal synchronous failures without persisting exception messages', function () {
    expect(fn () => Queue::push(new BackgroundJobFailingTestJob))
        ->toThrow(RuntimeException::class);

    $run = BackgroundJobRun::query()->sole();

    expect($run->status)->toBe(BackgroundJobRunStatus::FAILED)
        ->and($run->exception_class)->toBe(RuntimeException::class)
        ->and($run->error_message)->toBe('The job failed. See application logs for details.')
        ->and($run->failed_job_uuid)->not->toBeNull()
        ->and($run->error_message)->not->toContain('private-token');
});

it('continues queue execution when tracking persistence fails', function () {
    BackgroundJobRun::creating(static function (): void {
        throw new RuntimeException('tracking store unavailable');
    });
    BackgroundJobTrackingTestJob::$handled = false;

    Queue::push(new BackgroundJobTrackingTestJob);

    expect(BackgroundJobTrackingTestJob::$handled)->toBeTrue();
    $this->assertDatabaseCount('background_job_runs', 0);
});

final class BackgroundJobTrackingTestJob implements ShouldQueue
{
    use Queueable;

    public static bool $handled = false;

    public function handle(): void
    {
        self::$handled = true;
    }
}

final class BackgroundJobFailingTestJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        throw new RuntimeException('failed with private-token');
    }
}

final class BackgroundJobLifecycleTestJob extends SyncJob
{
    public function __construct(
        array $payload,
        private readonly int $attemptNumber,
        private readonly string $queueJobId,
    ) {
        parent::__construct(app(), json_encode($payload, JSON_THROW_ON_ERROR), 'database', 'default');
    }

    public function attempts(): int
    {
        return $this->attemptNumber;
    }

    public function getJobId(): string
    {
        return $this->queueJobId;
    }
}
