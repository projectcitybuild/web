<?php

namespace App\Domains\BackgroundJobs\Data;

enum BackgroundJobRunStatus: string
{
    case QUEUED = 'queued';
    case RUNNING = 'running';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
