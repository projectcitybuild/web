<?php

use App\Core\Data\JobQueue;
use App\Core\Domains\Scheduling\Jobs\RunScheduledTask;
use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Domains\BackgroundJobs\ScheduledTaskRegistry;
use App\Domains\HealthCheck\Jobs\QueueWorkerHeartbeatJob;
use App\Models\BackgroundJobRun;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
});

it('aggregates completed jobs into throughput buckets and queue totals', function () {
    $account = $this->adminAccount();
    $this->travelTo('2026-11-23 12:00:00');

    BackgroundJobRun::factory()->create([
        'job_name' => 'App\\Jobs\\SendMail',
        'queue' => JobQueue::MAIL->value,
        'status' => BackgroundJobRunStatus::SUCCEEDED,
        'queued_at' => now()->subMinutes(21),
        'started_at' => now()->subMinutes(20),
        'completed_at' => now()->subMinutes(20),
        'duration_ms' => 100,
    ]);
    BackgroundJobRun::factory()->create([
        'job_name' => 'App\\Jobs\\SendDiscordMessage',
        'queue' => JobQueue::DISCORD->value,
        'status' => BackgroundJobRunStatus::FAILED,
        'queued_at' => now()->subMinutes(26),
        'started_at' => now()->subMinutes(25),
        'completed_at' => now()->subMinutes(25),
        'failed_at' => now()->subMinutes(25),
        'duration_ms' => 300,
    ]);
    BackgroundJobRun::factory()->create([
        'status' => BackgroundJobRunStatus::SUCCEEDED,
        'completed_at' => now()->subDays(2),
    ]);
    BackgroundJobRun::factory()->create([
        'status' => BackgroundJobRunStatus::QUEUED,
        'queued_at' => now()->subMinutes(3),
    ]);
    BackgroundJobRun::factory()->create([
        'status' => BackgroundJobRunStatus::RUNNING,
        'queued_at' => now()->subMinute(),
        'started_at' => now()->subSeconds(30),
    ]);

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index', ['range' => '24h']))
        ->assertInertia(fn (Assert $page) => $page
            ->component('BackgroundJobs/Throughput')
            ->where('range', '24h')
            ->where('dashboard.summary.succeeded', 1)
            ->where('dashboard.summary.failed', 1)
            ->where('dashboard.summary.successRate', 50)
            ->where('dashboard.summary.averageDurationMs', 200)
            ->where('dashboard.buckets.94.succeeded', 1)
            ->where('dashboard.buckets.94.failed', 1)
            ->where('dashboard.queueBuckets.1.queue', JobQueue::MAIL->value)
            ->where('dashboard.queueBuckets.1.buckets.94.succeeded', 1)
            ->where('dashboard.queueBuckets.1.buckets.94.failed', 0)
            ->where('dashboard.queueBuckets.3.queue', JobQueue::DISCORD->value)
            ->where('dashboard.queueBuckets.3.buckets.94.succeeded', 0)
            ->where('dashboard.queueBuckets.3.buckets.94.failed', 1)
            ->where('dashboard.queueBreakdown.1.queue', JobQueue::MAIL->value)
            ->where('dashboard.queueBreakdown.1.count', 1)
            ->where('dashboard.queueBreakdown.3.queue', JobQueue::DISCORD->value)
            ->where('dashboard.queueBreakdown.3.count', 1)
            ->where('dashboard.queueHealth.pending', 1)
            ->where('dashboard.queueHealth.running', 1)
            ->where('dashboard.queueHealth.failed', 1)
            ->where('dashboard.queueHealth.oldestPendingAt', now()->subMinutes(3)->toISOString())
        );
});

it('returns empty metrics for a valid range with no completed jobs', function () {
    $account = $this->adminAccount();

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index', ['range' => '1h']))
        ->assertInertia(fn (Assert $page) => $page
            ->component('BackgroundJobs/Throughput')
            ->where('dashboard.summary.succeeded', 0)
            ->where('dashboard.summary.failed', 0)
            ->where('dashboard.summary.successRate', null)
            ->where('dashboard.summary.averageDurationMs', null)
            ->has('dashboard.buckets', 60)
            ->has('scheduledTasks')
            ->where('dashboard.queueBreakdown.0.count', 0)
        );
});

it('groups queue key prefixes under their logical queues', function () {
    $account = $this->adminAccount();

    BackgroundJobRun::factory()->create([
        'queue' => 'queue:default',
        'status' => BackgroundJobRunStatus::SUCCEEDED,
        'completed_at' => now()->subMinutes(2),
    ]);
    BackgroundJobRun::factory()->create([
        'queue' => 'queues:player-mail',
        'status' => BackgroundJobRunStatus::SUCCEEDED,
        'completed_at' => now()->subMinute(),
    ]);

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index', ['range' => '24h']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('dashboard.queueBreakdown.0.queue', JobQueue::DEFAULT->value)
            ->where('dashboard.queueBreakdown.0.label', 'Default')
            ->where('dashboard.queueBreakdown.0.count', 1)
            ->where('dashboard.queueBuckets.0.buckets.95.succeeded', 1)
            ->where('dashboard.queueBreakdown.2.queue', JobQueue::PLAYER_MAIL->value)
            ->where('dashboard.queueBreakdown.2.label', 'Player mail')
            ->where('dashboard.queueBreakdown.2.count', 1)
            ->where('dashboard.queueBuckets.2.buckets.95.succeeded', 1)
        );
});

it('lists scheduled tasks with their frequency and next run countdown', function () {
    $account = $this->adminAccount();
    $this->travelTo('2026-11-23 12:01:00');

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('scheduledTasks', fn (Collection $tasks): bool => $tasks->contains(
                fn (array $task): bool => $task['name'] === 'Check queue worker health'
                    && $task['subtitle'] === QueueWorkerHeartbeatJob::class
                    && $task['expression'] === '*/5 * * * *'
                    && $task['frequency'] === 'Every 5 minutes'
                    && $task['nextRunAt'] === '2026-11-23T12:05:00.000000Z'
                    && $task['secondsUntilNextRun'] === 240
            ))
        );
});

it('queues a scheduled task for execution by a queue worker', function () {
    $account = $this->adminAccount();
    $runs = 0;
    $event = app(Schedule::class)->call(function () use (&$runs): void {
        $runs++;
    })->name('background-jobs-queue-test')->everyMinute();
    $event->withAttributes(['admin_title' => 'Queue test task']);
    $task = collect(app(ScheduledTaskRegistry::class)->all())
        ->firstWhere('name', 'Queue test task');
    Queue::fake([RunScheduledTask::class]);

    $this->actingAs($account)
        ->post(route('auditing.background-jobs.scheduled-tasks.queue', $task['id']))
        ->assertRedirect()
        ->assertSessionHas('success', 'Scheduled task queued: Queue test task');

    Queue::assertPushed(
        RunScheduledTask::class,
        fn (RunScheduledTask $job): bool => $job->taskId === $task['id']
            && $job->displayName() === 'Queue test task',
    );
    expect($runs)->toBe(0);

    (new RunScheduledTask($task['id'], 'Queue test task'))->handle(app(ScheduledTaskRegistry::class));

    expect($runs)->toBe(1);
});

it('rejects unknown scheduled task identifiers without queueing work', function () {
    $account = $this->adminAccount();
    Queue::fake([RunScheduledTask::class]);

    $this->actingAs($account)
        ->post(route('auditing.background-jobs.scheduled-tasks.queue', str_repeat('0', 64)))
        ->assertNotFound();

    Queue::assertNothingPushed();
});

it('runs a scheduled task immediately when an administrator requests it', function () {
    $account = $this->adminAccount();
    $runs = 0;
    app(Schedule::class)->call(function () use (&$runs): void {
        $runs++;
    })->name('background-jobs-run-test')->daily();
    $task = collect(app(ScheduledTaskRegistry::class)->all())
        ->firstWhere('name', 'background-jobs-run-test');

    $this->actingAs($account)
        ->post(route('auditing.background-jobs.scheduled-tasks.run', $task['id']))
        ->assertRedirect();

    expect($runs)->toBe(1);
});

it('forbids staff from queueing or running scheduled tasks', function () {
    $account = $this->staffAccount();
    $runs = 0;
    app(Schedule::class)->call(function () use (&$runs): void {
        $runs++;
    })->name('background-jobs-staff-test')->everyMinute();
    $task = collect(app(ScheduledTaskRegistry::class)->all())
        ->firstWhere('name', 'background-jobs-staff-test');
    Queue::fake([RunScheduledTask::class]);

    $this->actingAs($account)
        ->post(route('auditing.background-jobs.scheduled-tasks.queue', $task['id']))
        ->assertForbidden();

    $this->post(route('auditing.background-jobs.scheduled-tasks.run', $task['id']))
        ->assertForbidden();

    Queue::assertNothingPushed();
    expect($runs)->toBe(0);
});

it('rejects unsupported throughput ranges', function () {
    $account = $this->adminAccount();

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index', ['range' => '365d']))
        ->assertSessionHasErrors('range');
});

it('filters and sorts the background job run list', function () {
    $account = $this->adminAccount();
    $zebra = BackgroundJobRun::factory()->create([
        'job_name' => 'App\\Jobs\\ZebraSync',
        'status' => BackgroundJobRunStatus::FAILED,
        'queue' => 'sync',
    ]);
    $alpha = BackgroundJobRun::factory()->create([
        'job_name' => 'App\\Jobs\\AlphaSync',
        'status' => BackgroundJobRunStatus::FAILED,
        'queue' => 'sync',
    ]);
    BackgroundJobRun::factory()->create([
        'job_name' => 'App\\Jobs\\Unrelated',
        'status' => BackgroundJobRunStatus::QUEUED,
        'queue' => 'sync',
    ]);

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.runs', [
            'search' => 'Sync',
            'status' => 'failed',
            'queue' => 'sync',
            'sort' => 'job_name',
            'direction' => 'asc',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BackgroundJobs/RunList')
            ->where('filters.search', 'Sync')
            ->where('filters.status', 'failed')
            ->where('filters.queue', 'sync')
            ->where('sort', 'job_name')
            ->where('direction', 'asc')
            ->has('runs.data', 2)
            ->where('runs.data.0.id', $alpha->id)
            ->where('runs.data.1.id', $zebra->id)
        );
});

it('forbids staff from viewing background job audits', function () {
    $account = $this->staffAccount();

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.index'))
        ->assertForbidden();

    $this->get(route('auditing.background-jobs.runs'))
        ->assertForbidden();
});

it('renders safe details for an authorized job run', function () {
    $account = $this->adminAccount();
    $run = BackgroundJobRun::factory()->create([
        'metadata' => [
            'discord_user_id' => '123456789012345678',
            'sync_id' => 'sync-123',
        ],
    ]);

    $this->actingAs($account)
        ->get(route('auditing.background-jobs.show', $run))
        ->assertInertia(fn (Assert $page) => $page
            ->component('BackgroundJobs/Show')
            ->where('run.id', $run->id)
            ->where('run.queue_label', 'Default')
            ->where('run.metadata.discord_user_id', '123456789012345678')
            ->missing('run.payload')
        );
});
