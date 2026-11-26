<?php

namespace App\Core\Domains\Discord\Jobs;

use App\Domains\BackgroundJobs\DescribesBackgroundJob;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class SyncDiscordRoleJob implements DescribesBackgroundJob, ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public array $backoff = [30, 120, 600];
    public int $timeout = 15;
    public int $uniqueFor = 1800;
    public readonly string $syncId;

    public function __construct(
        public readonly string $discordUserId,
        public readonly ?string $accountEvent = null,
    ) {
        $this->syncId = (string) Str::uuid();
    }

    public function handle(): void
    {
        $callbackUrl = config('services.discord.bot_callback_url');
        $secret = config('services.discord.callback_secret');

        if (! is_string($callbackUrl) || $callbackUrl === '' || ! is_string($secret) || $secret === '') {
            throw new RuntimeException('Discord bot callback configuration is incomplete.');
        }

        $callback = parse_url($callbackUrl);

        if (
            ! is_array($callback)
            || ! isset($callback['scheme'], $callback['host'])
            || ! in_array($callback['scheme'] ?? null, ['http', 'https'], true)
            || isset($callback['user'])
            || isset($callback['pass'])
        ) {
            throw new RuntimeException('Discord bot callback URL must be a valid HTTP or HTTPS URL.');
        }

        $isInternalBotHttpCallback = $callback['scheme'] === 'http'
            && strcasecmp($callback['host'], 'pcb-discord-bot') === 0;

        if (
            ! app()->environment(['local', 'testing'])
            && $callback['scheme'] !== 'https'
            && ! $isInternalBotHttpCallback
        ) {
            throw new RuntimeException('Discord bot callback URL must use HTTPS outside the internal bot service.');
        }

        $payload = [
            'discord_user_id' => $this->discordUserId,
            'sync_id' => $this->syncId,
        ];

        if ($this->accountEvent !== null) {
            $payload['account_event'] = $this->accountEvent;
        }

        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        $timestamp = (string) now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, $secret);

        $response = Http::connectTimeout(3)
            ->timeout(5)
            ->withHeaders([
                'Idempotency-Key' => $this->syncId,
                'X-Discord-Timestamp' => $timestamp,
                'X-Discord-Signature' => 'sha256='.$signature,
            ])
            ->withBody($body, 'application/json')
            ->post($callbackUrl);

        if ($response->successful()) {
            return;
        }

        $exception = new RuntimeException('Discord role sync callback returned HTTP '.$response->status().'.');

        if ($response->clientError() && $response->status() !== 429) {
            Log::error('Discord role sync callback was rejected.', [
                'discord_user_id' => $this->discordUserId,
                'sync_id' => $this->syncId,
                'status' => $response->status(),
            ]);

            $this->fail($exception);

            return;
        }

        throw $exception;
    }

    public function backgroundJobMetadata(): array
    {
        return [
            'discord_user_id' => $this->discordUserId,
            'sync_id' => $this->syncId,
        ];
    }

    public function uniqueId(): string
    {
        $key = 'discord-role-sync:'.$this->discordUserId;

        return $this->accountEvent === null
            ? $key
            : $key.':account-event:'.$this->accountEvent;
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Discord role sync callback failed after retries.', [
            'discord_user_id' => $this->discordUserId,
            'sync_id' => $this->syncId,
            'exception_class' => $exception !== null ? $exception::class : null,
        ]);
    }
}
