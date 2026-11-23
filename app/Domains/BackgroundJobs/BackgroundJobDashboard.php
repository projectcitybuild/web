<?php

namespace App\Domains\BackgroundJobs;

use App\Core\Data\JobQueue;
use App\Domains\BackgroundJobs\Data\BackgroundJobRange;
use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Models\BackgroundJobRun;
use Illuminate\Support\Carbon;
use RuntimeException;

class BackgroundJobDashboard
{
    /**
     * @return array{
     *     generatedAt: string,
     *     summary: array{succeeded: int, failed: int, successRate: float|null, averageDurationMs: int|null},
     *     buckets: list<array{timestamp: string, succeeded: int, failed: int}>,
     *     queueBuckets: list<array{queue: string, buckets: list<array{succeeded: int, failed: int}>}>,
     *     queueBreakdown: list<array{queue: string, label: string, count: int}>,
     *     recentRuns: list<array{id: string, job_name: string, status: string, queue: string|null, queue_label: string, attempts: int, max_attempts: int|null, duration_ms: int|null, queued_at: string|null, completed_at: string|null, created_at: string}>,
     *     queueHealth: array{pending: int, running: int, oldestPendingAt: string|null, failed: int, driver: string}
     * }
     */
    public function forRange(BackgroundJobRange $range): array
    {
        $now = now('UTC');
        $from = $now->copy()->subSeconds($range->seconds());
        $bucketSeconds = $range->bucketSeconds();
        $startTimestamp = $from->timestamp;
        $bucketExpression = $this->bucketExpression();
        $statuses = [
            BackgroundJobRunStatus::SUCCEEDED->value,
            BackgroundJobRunStatus::FAILED->value,
        ];

        $bucketRows = BackgroundJobRun::query()
            ->whereIn('status', $statuses)
            ->whereBetween('completed_at', [$from, $now])
            ->selectRaw(
                $bucketExpression.' AS bucket, '
                    .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS succeeded, '
                    .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS failed',
                [$startTimestamp, $bucketSeconds, $bucketSeconds, $startTimestamp, ...$statuses],
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $queueRows = BackgroundJobRun::query()
            ->whereIn('status', $statuses)
            ->whereBetween('completed_at', [$from, $now])
            ->selectRaw('COALESCE(queue, ?) AS normalized_queue, COUNT(*) AS run_count', [JobQueue::DEFAULT->value])
            ->groupBy('normalized_queue')
            ->orderBy('normalized_queue')
            ->get();

        $queueBucketRows = BackgroundJobRun::query()
            ->whereIn('status', $statuses)
            ->whereBetween('completed_at', [$from, $now])
            ->selectRaw(
                $bucketExpression.' AS bucket, COALESCE(queue, ?) AS normalized_queue, '
                    .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS succeeded, '
                    .'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS failed',
                [$startTimestamp, $bucketSeconds, $bucketSeconds, $startTimestamp, JobQueue::DEFAULT->value, ...$statuses],
            )
            ->groupBy('bucket', 'normalized_queue')
            ->orderBy('bucket')
            ->get();

        $duration = BackgroundJobRun::query()
            ->whereIn('status', $statuses)
            ->whereBetween('completed_at', [$from, $now])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS succeeded_count', [BackgroundJobRunStatus::SUCCEEDED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS failed_count', [BackgroundJobRunStatus::FAILED->value])
            ->selectRaw('AVG(duration_ms) AS average_duration_ms')
            ->first();

        $buckets = [];
        $queueBucketCounts = array_fill_keys(
            array_map(fn (JobQueue $queue): string => $queue->value, JobQueue::cases()),
            [],
        );
        $queueTotals = array_fill_keys(
            array_map(fn (JobQueue $queue): string => $queue->value, JobQueue::cases()),
            0,
        );
        $customQueueTotals = [];

        for ($index = 0; $index < intdiv($range->seconds(), $bucketSeconds); $index++) {
            $buckets[$index] = [
                'timestamp' => $from->copy()->addSeconds($index * $bucketSeconds)->toISOString(),
                'succeeded' => 0,
                'failed' => 0,
            ];

            foreach (array_keys($queueBucketCounts) as $queue) {
                $queueBucketCounts[$queue][$index] = [
                    'succeeded' => 0,
                    'failed' => 0,
                ];
            }
        }

        foreach ($bucketRows as $row) {
            $bucketIndex = intdiv(((int) $row->bucket) - $startTimestamp, $bucketSeconds);

            if (! array_key_exists($bucketIndex, $buckets)) {
                continue;
            }

            $buckets[$bucketIndex]['succeeded'] = (int) $row->succeeded;
            $buckets[$bucketIndex]['failed'] = (int) $row->failed;

        }

        foreach ($queueBucketRows as $row) {
            $bucketIndex = intdiv(((int) $row->bucket) - $startTimestamp, $bucketSeconds);

            if (! array_key_exists($bucketIndex, $buckets)) {
                continue;
            }

            $queue = JobQueue::normalize((string) $row->normalized_queue);
            $queueBucketCounts[$queue] ??= array_fill(0, count($buckets), [
                'succeeded' => 0,
                'failed' => 0,
            ]);
            $queueBucketCounts[$queue][$bucketIndex] = [
                'succeeded' => (int) $row->succeeded,
                'failed' => (int) $row->failed,
            ];
        }

        foreach ($queueRows as $row) {
            $queue = JobQueue::normalize((string) $row->normalized_queue);
            $count = (int) $row->run_count;

            if (array_key_exists($queue, $queueTotals)) {
                $queueTotals[$queue] += $count;
            } else {
                $customQueueTotals[$queue] = ($customQueueTotals[$queue] ?? 0) + $count;
            }
        }

        $succeeded = (int) ($duration?->succeeded_count ?? 0);
        $failed = (int) ($duration?->failed_count ?? 0);
        $terminalCount = $succeeded + $failed;
        $health = BackgroundJobRun::query()
            ->whereIn('status', [
                BackgroundJobRunStatus::QUEUED->value,
                BackgroundJobRunStatus::RUNNING->value,
                BackgroundJobRunStatus::FAILED->value,
            ])
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_count',
                [BackgroundJobRunStatus::QUEUED->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS running_count',
                [BackgroundJobRunStatus::RUNNING->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS failed_count',
                [BackgroundJobRunStatus::FAILED->value],
            )
            ->selectRaw(
                'MIN(CASE WHEN status = ? THEN queued_at END) AS oldest_pending_at',
                [BackgroundJobRunStatus::QUEUED->value],
            )
            ->first();

        $oldestPendingAt = $health?->oldest_pending_at === null
            ? null
            : Carbon::parse($health->oldest_pending_at, 'UTC')->toISOString();
        $recentRuns = BackgroundJobRun::query()
            ->latest('created_at')
            ->limit(8)
            ->get([
                'id',
                'job_name',
                'status',
                'queue',
                'attempts',
                'max_attempts',
                'duration_ms',
                'queued_at',
                'completed_at',
                'created_at',
            ])
            ->map(fn (BackgroundJobRun $run): array => [
                'id' => $run->getKey(),
                'job_name' => $run->job_name,
                'status' => $run->status->value,
                'queue' => $run->queue,
                'queue_label' => JobQueue::labelFor($run->queue),
                'attempts' => $run->attempts,
                'max_attempts' => $run->max_attempts,
                'duration_ms' => $run->duration_ms,
                'queued_at' => $run->queued_at?->toISOString(),
                'completed_at' => $run->completed_at?->toISOString(),
                'created_at' => $run->created_at->toISOString(),
            ])
            ->all();

        return [
            'generatedAt' => $now->toISOString(),
            'summary' => [
                'succeeded' => $succeeded,
                'failed' => $failed,
                'successRate' => $terminalCount === 0
                    ? null
                    : round(($succeeded / $terminalCount) * 100, 1),
                'averageDurationMs' => $duration?->average_duration_ms === null
                    ? null
                    : (int) round((float) $duration->average_duration_ms),
            ],
            'buckets' => array_values($buckets),
            'queueBuckets' => array_map(
                fn (string $queue, array $queueBuckets): array => [
                    'queue' => $queue,
                    'buckets' => array_values($queueBuckets),
                ],
                array_keys($queueBucketCounts),
                array_values($queueBucketCounts),
            ),
            'queueBreakdown' => [
                ...array_map(
                    fn (JobQueue $queue): array => [
                        'queue' => $queue->value,
                        'label' => $queue->label(),
                        'count' => $queueTotals[$queue->value],
                    ],
                    JobQueue::cases(),
                ),
                ...array_map(
                    fn (string $queue, int $count): array => [
                        'queue' => $queue,
                        'label' => JobQueue::labelFor($queue),
                        'count' => $count,
                    ],
                    array_keys($customQueueTotals),
                    array_values($customQueueTotals),
                ),
            ],
            'recentRuns' => $recentRuns,
            'queueHealth' => [
                'pending' => (int) ($health?->pending_count ?? 0),
                'running' => (int) ($health?->running_count ?? 0),
                'oldestPendingAt' => $oldestPendingAt,
                'failed' => (int) ($health?->failed_count ?? 0),
                'driver' => (string) config('queue.connections.'.config('queue.default').'.driver', 'unknown'),
            ],
        ];
    }

    private function bucketExpression(): string
    {
        $timestampExpression = match (app('db')->connection()->getDriverName()) {
            'mysql', 'mariadb' => 'UNIX_TIMESTAMP(completed_at)',
            'sqlite' => "CAST(strftime('%s', completed_at) AS INTEGER)",
            default => throw new RuntimeException('Background job throughput is not supported by this database driver.'),
        };

        return 'FLOOR(('.$timestampExpression.' - ?) / ?) * ? + ?';
    }
}
