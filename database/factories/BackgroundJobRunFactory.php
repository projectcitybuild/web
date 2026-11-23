<?php

namespace Database\Factories;

use App\Domains\BackgroundJobs\Data\BackgroundJobRunStatus;
use App\Models\BackgroundJobRun;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BackgroundJobRun>
 */
class BackgroundJobRunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'job_name' => 'App\\Jobs\\ExampleJob',
            'job_type' => 'other',
            'status' => BackgroundJobRunStatus::QUEUED,
            'queue' => 'default',
            'metadata' => null,
            'attempts' => 0,
            'max_attempts' => null,
            'queued_at' => now()->utc(),
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'duration_ms' => null,
            'exception_class' => null,
            'error_message' => null,
        ];
    }
}
