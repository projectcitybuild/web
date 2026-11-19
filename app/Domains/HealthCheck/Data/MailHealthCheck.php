<?php

namespace App\Domains\HealthCheck\Data;

use App\Domains\HealthCheck\HealthCheck;

class MailHealthCheck implements HealthCheck
{
    public function enabled(): bool
    {
        return config('healthcheck.checks.mail') !== null;
    }

    public function url(): string
    {
        return config('healthcheck.checks.mail');
    }
}
