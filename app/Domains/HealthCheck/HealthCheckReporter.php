<?php

namespace App\Domains\HealthCheck;

use Illuminate\Support\Facades\Http;

/**
 * @see https://healthchecks.io/docs/http_api/
 */
class HealthCheckReporter
{
    public function __construct(
        private readonly HealthCheck $healthCheck,
    ) {}

    public function success(): void
    {
        Http::retry(times: 3, sleepMilliseconds: 100)->post(
            $this->healthCheck->url(),
        );
    }
}
