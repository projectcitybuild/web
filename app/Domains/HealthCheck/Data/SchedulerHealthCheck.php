<?php

namespace App\Domains\HealthCheck\Data;

use App\Domains\HealthCheck\HealthCheck;

class SchedulerHealthCheck implements HealthCheck
{
    public function enabled(): bool
    {
        return config('healthcheck.checks.scheduler') !== null;
    }

    public function url(): string
    {
        return config('healthcheck.checks.scheduler');
    }
}
