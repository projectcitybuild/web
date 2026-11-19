<?php

use App\Domains\HealthCheck\Jobs\QueueWorkerHeartbeatJob;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

it('pings the queue monitor after the worker executes the job', function () {
    config(['healthcheck.checks.queue' => 'https://hc-ping.com/queue-token']);
    Http::fake([
        'https://hc-ping.com/queue-token' => Http::response(),
    ]);

    (new QueueWorkerHeartbeatJob)->handle();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://hc-ping.com/queue-token');
});

it('does not ping when the queue monitor is not configured', function () {
    config(['healthcheck.checks.queue' => null]);
    Http::fake();

    (new QueueWorkerHeartbeatJob)->handle();

    Http::assertNothingSent();
});

it('uses the default queue', function () {
    expect((new QueueWorkerHeartbeatJob)->queue)->toBe('default');
});

it('fails on unsuccessful monitor responses without exposing the ping URL', function () {
    config(['healthcheck.checks.queue' => 'https://hc-ping.com/private-queue-token']);
    Http::fake([
        'https://hc-ping.com/private-queue-token' => Http::response(status: 500),
    ]);

    expect(fn () => (new QueueWorkerHeartbeatJob)->handle())
        ->toThrow(RuntimeException::class, 'HTTP status 500');

    Http::assertSentCount(3);
});
