<?php

namespace App\Domains\BackgroundJobs;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class BackgroundJobsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Queue::createPayloadUsing(
            static fn (string $connectionName, ?string $queue, array $payload): array => app(BackgroundJobRunTracker::class)
                ->creatingPayload($connectionName, $queue, $payload),
        );

        Queue::before(
            static fn (JobProcessing $event) => app(BackgroundJobRunTracker::class)->processing($event->job),
        );

        Queue::after(
            static fn (JobProcessed $event) => app(BackgroundJobRunTracker::class)->processed($event->job),
        );

        Event::listen(
            JobExceptionOccurred::class,
            static fn (JobExceptionOccurred $event) => app(BackgroundJobRunTracker::class)->exceptionOccurred($event->job),
        );

        Event::listen(
            JobFailed::class,
            static fn (JobFailed $event) => app(BackgroundJobRunTracker::class)->failed($event->job, $event->exception),
        );

        Event::listen(
            JobRetryRequested::class,
            static fn (JobRetryRequested $event) => app(BackgroundJobRunTracker::class)->retryRequested($event->job),
        );
    }
}
