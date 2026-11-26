<?php

namespace App\Domains\BackgroundJobs;

use App\Core\Data\JobQueue;
use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Models\BackgroundJobRun;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use stdClass;
use Throwable;

class BackgroundJobRunTracker
{
    /**
     * Add a stable tracking identifier to a new Laravel queue payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, string>
     */
    public function creatingPayload(string $connectionName, ?string $queue, array $payload): array
    {
        $id = (string) Str::uuid();

        try {
            $description = $this->describe($payload);

            BackgroundJobRun::query()->create([
                'id' => $id,
                'job_name' => $description['name'],
                'job_type' => 'other',
                'status' => BackgroundJobRunStatus::QUEUED,
                'queue' => $queue === null
                    ? JobQueue::DEFAULT->value
                    : JobQueue::normalize($queue),
                'metadata' => $description['metadata'] === []
                    ? null
                    : $description['metadata'],
                'attempts' => 0,
                'max_attempts' => is_numeric($payload['maxTries'] ?? null)
                    ? (int) $payload['maxTries']
                    : null,
                'queued_at' => now()->utc(),
            ]);

            return ['background_job_run_id' => $id];
        } catch (Throwable $exception) {
            $this->logTrackingFailure('queueing', $exception);

            return [];
        }
    }

    public function processing(Job $job): void
    {
        $this->updateTrackedRun($job, 'processing', function (BackgroundJobRun $run) use ($job): void {
            if (in_array($run->status, [BackgroundJobRunStatus::SUCCEEDED, BackgroundJobRunStatus::FAILED], true)) {
                return;
            }

            $run->update([
                'status' => BackgroundJobRunStatus::RUNNING,
                'queue_job_id' => $this->queueJobId($job),
                'failed_job_uuid' => null,
                'attempts' => $job->attempts(),
                'max_attempts' => method_exists($job, 'maxTries') ? $job->maxTries() : $run->max_attempts,
                'started_at' => now()->utc(),
                'completed_at' => null,
                'failed_at' => null,
                'duration_ms' => null,
            ]);
        });
    }

    public function processed(Job $job): void
    {
        $this->updateTrackedRun($job, 'completion', function (BackgroundJobRun $run) use ($job): void {
            if (method_exists($job, 'hasFailed') && $job->hasFailed()) {
                return;
            }

            if (method_exists($job, 'isReleased') && $job->isReleased()) {
                $run->update([
                    'status' => BackgroundJobRunStatus::QUEUED,
                    'attempts' => $job->attempts(),
                ]);

                return;
            }

            if (in_array($run->status, [BackgroundJobRunStatus::SUCCEEDED, BackgroundJobRunStatus::FAILED], true)) {
                return;
            }

            $completedAt = now()->utc();

            $run->update([
                'status' => BackgroundJobRunStatus::SUCCEEDED,
                'attempts' => $job->attempts(),
                'completed_at' => $completedAt,
                'duration_ms' => $this->durationMilliseconds($run, $completedAt),
                'failed_job_uuid' => null,
            ]);
        });
    }

    public function exceptionOccurred(Job $job): void
    {
        $this->updateTrackedRun($job, 'retry scheduling', function (BackgroundJobRun $run) use ($job): void {
            if (in_array($run->status, [BackgroundJobRunStatus::SUCCEEDED, BackgroundJobRunStatus::FAILED], true)) {
                return;
            }

            $run->update([
                'status' => BackgroundJobRunStatus::QUEUED,
                'attempts' => $job->attempts(),
            ]);
        });
    }

    public function failed(Job $job, ?Throwable $exception): void
    {
        $this->updateTrackedRun($job, 'failure', function (BackgroundJobRun $run) use ($job, $exception): void {
            $failedAt = now()->utc();

            $run->update([
                'status' => BackgroundJobRunStatus::FAILED,
                'queue_job_id' => $this->queueJobId($job),
                'failed_job_uuid' => $this->failedJobUuid($job),
                'attempts' => $job->attempts(),
                'completed_at' => $failedAt,
                'failed_at' => $failedAt,
                'duration_ms' => $this->durationMilliseconds($run, $failedAt),
                'exception_class' => $exception !== null ? $exception::class : null,
                'error_message' => 'The job failed. See application logs for details.',
            ]);
        });
    }

    public function retryRequested(stdClass $failedJob): void
    {
        $payload = json_decode($failedJob->payload, true);
        $id = is_array($payload) ? ($payload['background_job_run_id'] ?? null) : null;

        if (! is_string($id) || ! Str::isUuid($id)) {
            return;
        }

        BackgroundJobRun::query()
            ->whereKey($id)
            ->where('status', BackgroundJobRunStatus::FAILED)
            ->update([
                'status' => BackgroundJobRunStatus::QUEUED,
                'attempts' => 0,
                'queued_at' => now()->utc(),
                'started_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'duration_ms' => null,
                'exception_class' => null,
                'error_message' => null,
            ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{name: string, metadata: array<string, scalar|null>}
     */
    private function describe(array $payload): array
    {
        $command = $payload['data']['command'] ?? null;
        $name = $payload['displayName'] ?? (is_object($command) ? $command::class : 'Unknown job');
        $name = is_string($name) && $name !== '' ? $name : 'Unknown job';

        if ($command instanceof DescribesBackgroundJob) {
            return [
                'name' => $name,
                'metadata' => $command->backgroundJobMetadata(),
            ];
        }

        return [
            'name' => $name,
            'metadata' => [],
        ];
    }

    private function updateTrackedRun(Job $job, string $phase, callable $update): void
    {
        try {
            $payload = $job->payload();
            $id = is_array($payload) ? ($payload['background_job_run_id'] ?? null) : null;

            if (! is_string($id) || ! Str::isUuid($id)) {
                return;
            }

            $run = BackgroundJobRun::query()->find($id);

            if ($run !== null) {
                $update($run);
            }
        } catch (Throwable $exception) {
            $this->logTrackingFailure($phase, $exception);
        }
    }

    private function queueJobId(Job $job): ?string
    {
        $id = $job->getJobId();

        return $id === null || (string) $id === '' ? null : (string) $id;
    }

    private function failedJobUuid(Job $job): ?string
    {
        $uuid = $job->payload()['uuid'] ?? null;

        return is_string($uuid) && $uuid !== '' ? $uuid : null;
    }

    private function durationMilliseconds(BackgroundJobRun $run, \DateTimeInterface $completedAt): ?int
    {
        if ($run->started_at === null) {
            return null;
        }

        return max(0, (int) $run->started_at->diffInMilliseconds($completedAt));
    }

    private function logTrackingFailure(string $phase, Throwable $exception): void
    {
        Log::warning('Background job monitoring could not update a run.', [
            'phase' => $phase,
            'exception_class' => $exception::class,
        ]);
    }
}
