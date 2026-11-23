<?php

namespace App\Models;

use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use Database\Factories\BackgroundJobRunFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class BackgroundJobRun extends Model
{
    /** @use HasFactory<BackgroundJobRunFactory> */
    use HasFactory;
    use Prunable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'queue_job_id',
        'job_name',
        'job_type',
        'status',
        'queue',
        'metadata',
        'attempts',
        'max_attempts',
        'queued_at',
        'started_at',
        'completed_at',
        'failed_at',
        'duration_ms',
        'exception_class',
        'error_message',
    ];
    protected $casts = [
        'status' => BackgroundJobRunStatus::class,
        'metadata' => 'array',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'duration_ms' => 'integer',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function prunable(): Builder
    {
        return self::where('created_at', '<=', now()->subMonths(3));
    }
}
