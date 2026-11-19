<?php

namespace App\Domains\HealthCheck\Data;

use App\Domains\HealthCheck\HealthCheck;

class QueueHealthCheck implements HealthCheck
{
    public function enabled(): bool
    {
        return config('healthcheck.checks.queue') !== null;
    }

    public function url(): string
    {
        return config('healthcheck.checks.queue');
    }
}
