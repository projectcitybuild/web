<?php

use App\Domains\HealthCheck\Jobs\MailCanaryJob;
use Illuminate\Http\Client\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;
use RuntimeException;

it('sends a plain-text canary before pinging its monitor', function () {
    config([
        'healthcheck.mail.enabled' => true,
        'healthcheck.mail.to' => 'monitor@example.test',
        'healthcheck.checks.mail' => 'https://hc-ping.com/mail-token',
    ]);
    $mailWasAccepted = false;

    Mail::shouldReceive('raw')
        ->once()
        ->withArgs(function (string $body, Closure $callback) use (&$mailWasAccepted): bool {
            expect($body)->toContain('Mail delivery canary');
            expect($body)->toMatch('/Canary ID: [0-9a-f-]{36}/');
            expect($body)->toContain('Sent at: '.now()->toIso8601String());

            $message = Mockery::mock(Message::class);
            $message->shouldReceive('to')->once()->with('monitor@example.test')->andReturnSelf();
            $message->shouldReceive('subject')
                ->once()
                ->with('Project City Build - Mail Delivery Canary')
                ->andReturnSelf();
            $callback($message);
            $mailWasAccepted = true;

            return true;
        });
    Http::fake([
        'https://hc-ping.com/mail-token' => function (Request $request) use (&$mailWasAccepted) {
            expect($mailWasAccepted)->toBeTrue();

            return Http::response();
        },
    ]);

    (new MailCanaryJob)->handle();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://hc-ping.com/mail-token');
});

it('does not send or ping when the canary is disabled or required configuration is missing', function (array $overrides) {
    config([
        'healthcheck.mail.enabled' => true,
        'healthcheck.mail.to' => 'monitor@example.test',
        'healthcheck.checks.mail' => 'https://hc-ping.com/mail-token',
        ...$overrides,
    ]);
    Mail::shouldReceive('raw')->never();
    Http::fake();

    (new MailCanaryJob)->handle();

    Http::assertNothingSent();
})->with([
    'disabled' => [['healthcheck.mail.enabled' => false]],
    'without a recipient' => [['healthcheck.mail.to' => null]],
    'without a monitor' => [['healthcheck.checks.mail' => null]],
]);

it('rethrows mail transport failures and does not ping the monitor', function () {
    config([
        'healthcheck.mail.enabled' => true,
        'healthcheck.mail.to' => 'monitor@example.test',
        'healthcheck.checks.mail' => 'https://hc-ping.com/mail-token',
    ]);
    Mail::shouldReceive('raw')->once()->andThrow(new RuntimeException('mail transport rejected the message'));
    Log::shouldReceive('error')->once()->with('Health check mail canary failed to send.', Mockery::type('array'));
    Http::fake();

    expect(fn () => (new MailCanaryJob)->handle())
        ->toThrow(RuntimeException::class, 'mail transport rejected the message');

    Http::assertNothingSent();
});

it('does not retry the accepted email when the monitor ping fails', function () {
    config([
        'healthcheck.mail.enabled' => true,
        'healthcheck.mail.to' => 'monitor@example.test',
        'healthcheck.checks.mail' => 'https://hc-ping.com/mail-token',
    ]);
    Mail::shouldReceive('raw')->once();
    Http::fake([
        'https://hc-ping.com/mail-token' => Http::response(status: 500),
    ]);

    (new MailCanaryJob)->handle();

    Http::assertSentCount(3);
});

it('uses the existing mail queue', function () {
    expect((new MailCanaryJob)->queue)->toBe('mail');
});
