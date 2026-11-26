<?php

namespace App\Http\Controllers\Admin\Auditing;

use App\Core\Data\JobQueue;
use App\Core\Domains\Scheduling\Jobs\RunScheduledTask;
use App\Domains\BackgroundJobs\BackgroundJobDashboard;
use App\Domains\BackgroundJobs\Data\BackgroundJobRange;
use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Domains\BackgroundJobs\ScheduledTaskRegistry;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Models\BackgroundJobRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Inertia\Response;

final class BackgroundJobController extends WebController
{
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(
        Request $request,
        BackgroundJobDashboard $dashboard,
        ScheduledTaskRegistry $scheduledTasks,
    ): Response {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'range' => ['nullable', 'string', Rule::in(array_map(
                fn (BackgroundJobRange $range): string => $range->value,
                BackgroundJobRange::cases(),
            ))],
        ]);
        $range = BackgroundJobRange::from($validated['range'] ?? BackgroundJobRange::LAST_DAY->value);

        return $this->inertiaRender('BackgroundJobs/Throughput', [
            'range' => $range->value,
            'dashboard' => $dashboard->forRange($range),
            'scheduledTasks' => $scheduledTasks->all(),
        ]);
    }

    public function queueTask(Request $request, string $taskId, ScheduledTaskRegistry $scheduledTasks): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $task = $scheduledTasks->find($taskId);
        abort_if($task === null, 404);

        $taskName = (string) ($task->attributes['admin_title'] ?? $task->getSummaryForDisplay());

        RunScheduledTask::dispatch($taskId, $taskName);

        return back()->with('success', 'Scheduled task queued: '.$taskName);
    }

    public function runTask(Request $request, string $taskId, ScheduledTaskRegistry $scheduledTasks): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $task = $scheduledTasks->find($taskId);
        abort_if($task === null, 404);

        $scheduledTasks->run($taskId);

        $taskName = (string) ($task->attributes['admin_title'] ?? $task->getSummaryForDisplay());

        return back()->with('success', 'Scheduled task run: '.$taskName);
    }

    public function runs(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(array_map(
                fn (BackgroundJobRunStatus $status): string => $status->value,
                BackgroundJobRunStatus::cases(),
            ))],
            'queue' => ['nullable', 'string', 'max:255'],
        ]);
        $sortableColumns = [
            'job_name' => 'job_name',
            'queue' => 'queue',
            'status' => 'status',
            'attempts' => 'attempts',
            'duration_ms' => 'duration_ms',
            'queued_at' => 'queued_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);
        $query = BackgroundJobRun::query()
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(
                fn ($query) => $query
                    ->where('job_name', 'like', '%'.$search.'%')
                    ->orWhere('queue', 'like', '%'.$search.'%')
            ))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['queue'] ?? null, fn ($query, string $queue) => $query->where('queue', $queue));

        $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

        $runs = $query->paginate(50, pageName: 'run_page')
            ->withQueryString()
            ->through(fn (BackgroundJobRun $run): array => [
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
                'show_url' => route('auditing.background-jobs.show', $run),
            ]);

        return $this->inertiaRender('BackgroundJobs/RunList', [
            'runs' => $runs,
            'queueOptions' => collect(JobQueue::cases())
                ->map(fn (JobQueue $queue): array => [
                    'value' => $queue->value,
                    'label' => $queue->label(),
                ])
                ->concat(BackgroundJobRun::query()
                    ->whereNotNull('queue')
                    ->distinct()
                    ->orderBy('queue')
                    ->pluck('queue')
                    ->map(fn (string $queue): array => [
                        'value' => $queue,
                        'label' => JobQueue::labelFor($queue),
                    ]))
                ->unique('value')
                ->values()
                ->all(),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
                'queue' => $filters['queue'] ?? '',
            ],
            'indexUrl' => route('auditing.background-jobs.runs'),
            ...$sortState,
        ]);
    }

    public function show(Request $request, BackgroundJobRun $run): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $canRetry = $run->status === BackgroundJobRunStatus::FAILED
            && $run->failed_job_uuid !== null
            && app('queue.failer')->find($run->failed_job_uuid) !== null;

        return $this->inertiaRender('BackgroundJobs/Show', [
            'can_retry' => $canRetry,
            'run' => [
                'id' => $run->getKey(),
                'job_name' => $run->job_name,
                'status' => $run->status->value,
                'queue' => $run->queue,
                'queue_label' => JobQueue::labelFor($run->queue),
                'attempts' => $run->attempts,
                'max_attempts' => $run->max_attempts,
                'queued_at' => $run->queued_at?->toISOString(),
                'started_at' => $run->started_at?->toISOString(),
                'completed_at' => $run->completed_at?->toISOString(),
                'duration_ms' => $run->duration_ms,
                'metadata' => $run->metadata ?? [],
                'exception_class' => $run->exception_class,
                'error_message' => $run->error_message,
            ],
        ]);
    }

    public function retry(Request $request, BackgroundJobRun $run): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        abort_unless($run->status === BackgroundJobRunStatus::FAILED, 409);

        $failedJobUuid = $run->failed_job_uuid;
        abort_if($failedJobUuid === null || app('queue.failer')->find($failedJobUuid) === null, 404);

        Artisan::call('queue:retry', ['id' => $failedJobUuid]);

        return back()->with('success', 'Failed job queued for retry: '.$run->job_name);
    }
}
