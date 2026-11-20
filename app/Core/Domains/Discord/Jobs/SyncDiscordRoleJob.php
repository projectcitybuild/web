<?php

namespace App\Core\Domains\Discord\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class SyncDiscordRoleJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public array $backoff = [30, 120, 600];
    public int $timeout = 15;
    public int $uniqueFor = 1800;
    public readonly string $syncId;

    public function __construct(
        public readonly string $discordUserId,
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
            || ! isset($callback['host'])
            || ! in_array($callback['scheme'] ?? null, ['http', 'https'], true)
            || isset($callback['user'])
            || isset($callback['pass'])
            || (! app()->environment(['local', 'testing']) && ($callback['scheme'] ?? null) !== 'https')
        ) {
            throw new RuntimeException('Discord bot callback URL must be a valid HTTPS URL.');
        }

        $body = json_encode([
            'discord_user_id' => $this->discordUserId,
            'sync_id' => $this->syncId,
        ], JSON_THROW_ON_ERROR);

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

        if ($response->clientError() && $response->status() !== 429) {
            Log::error('Discord role sync callback was rejected.', [
                'discord_user_id' => $this->discordUserId,
                'sync_id' => $this->syncId,
                'status' => $response->status(),
            ]);

            return;
        }

        $response->throw();
    }

    public function uniqueId(): string
    {
        return 'discord-role-sync:'.$this->discordUserId;
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
