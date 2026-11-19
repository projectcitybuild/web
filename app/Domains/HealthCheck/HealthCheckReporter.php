<?php

namespace App\Domains\HealthCheck;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

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
        try {
            Http::retry(times: 3, sleepMilliseconds: 100)
                ->connectTimeout(3)
                ->timeout(5)
                ->post($this->healthCheck->url())
                ->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException(
                'Healthchecks.io ping failed with HTTP status '.$exception->response->status().'.',
            );
        } catch (ConnectionException) {
            throw new RuntimeException('Healthchecks.io ping failed due to a connection error.');
        } catch (Throwable) {
            throw new RuntimeException('Healthchecks.io ping failed.');
        }
    }
}
