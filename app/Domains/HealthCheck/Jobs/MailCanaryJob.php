<?php

namespace App\Domains\HealthCheck\Jobs;

use App\Domains\HealthCheck\Data\MailHealthCheck;
use App\Domains\HealthCheck\HealthCheckReporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class MailCanaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        $healthCheck = new MailHealthCheck;
        $recipient = config('healthcheck.mail.to');

        if (
            ! config('healthcheck.mail.enabled')
            || ! is_string($recipient)
            || $recipient === ''
            || ! $healthCheck->enabled()
        ) {
            return;
        }

        $canaryId = (string) Str::uuid();
        $sentAt = now()->toIso8601String();

        try {
            Mail::raw(
                "Mail delivery canary\nCanary ID: {$canaryId}\nSent at: {$sentAt}\n",
                function (Message $message) use ($recipient): void {
                    $message
                        ->to($recipient)
                        ->subject('Project City Build - Mail Delivery Canary');
                },
            );
        } catch (Throwable $exception) {
            Log::error('Health check mail canary failed to send.', [
                'canary_id' => $canaryId,
                'recipient' => $recipient,
                'mailer' => config('mail.default'),
                'exception_class' => $exception::class,
            ]);

            throw $exception;
        }

        try {
            (new HealthCheckReporter($healthCheck))->success();
        } catch (Throwable $exception) {
            Log::error('Health check mail canary was sent, but its monitor ping failed.', [
                'canary_id' => $canaryId,
                'exception_class' => $exception::class,
            ]);
        }
    }
}
