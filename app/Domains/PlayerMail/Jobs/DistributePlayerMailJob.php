<?php

namespace App\Domains\PlayerMail\Jobs;

use App\Core\Data\JobQueue;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMailBroadcast;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class DistributePlayerMailJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    private const BATCH_SIZE = 500;

    public int $tries = 3;
    public int $timeout = 60;
    public int $uniqueFor = 3600;
    public array $backoff = [5, 30, 90];

    public function __construct(
        public readonly int $broadcastId,
        public readonly int $expectedLastPlayerId,
    ) {
        $this->onQueue(JobQueue::PLAYER_MAIL->value);
    }

    public function handle(): void
    {
        $nextPlayerId = DB::transaction(function (): ?int {
            $broadcast = PlayerMailBroadcast::query()
                ->lockForUpdate()
                ->findOrFail($this->broadcastId);

            if (
                $broadcast->status === PlayerMailBroadcastStatus::COMPLETED
                || $broadcast->status === PlayerMailBroadcastStatus::FAILED
                || $broadcast->last_player_id !== $this->expectedLastPlayerId
            ) {
                return null;
            }

            if ($broadcast->status === PlayerMailBroadcastStatus::QUEUED) {
                $broadcast->status = PlayerMailBroadcastStatus::PROCESSING;
                $broadcast->started_at = now();
            }

            $playerIds = MinecraftPlayer::query()
                ->where('id', '>', $broadcast->last_player_id)
                ->where('id', '<=', $broadcast->max_player_id)
                ->orderBy('id')
                ->limit(self::BATCH_SIZE)
                ->pluck('id');

            if ($playerIds->isEmpty()) {
                $broadcast->status = PlayerMailBroadcastStatus::COMPLETED;
                $broadcast->completed_at = now();
                $broadcast->save();

                return null;
            }

            $timestamp = now();
            $recipients = $playerIds->map(fn (int $playerId): array => [
                'mail_id' => $broadcast->mail_id,
                'player_id' => $playerId,
                'received_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all();

            $insertedRecipients = DB::table('player_mail_recipients')->insertOrIgnore($recipients);
            $nextPlayerId = (int) $playerIds->last();

            $broadcast->last_player_id = $nextPlayerId;
            $broadcast->recipients_count += $insertedRecipients;
            $broadcast->save();

            return $nextPlayerId;
        }, attempts: 3);

        if ($nextPlayerId !== null) {
            self::dispatch($this->broadcastId, $nextPlayerId);
        }
    }

    public function uniqueId(): string
    {
        return $this->broadcastId.':'.$this->expectedLastPlayerId;
    }

    public function failed(?Throwable $exception): void
    {
        PlayerMailBroadcast::query()
            ->whereKey($this->broadcastId)
            ->whereIn('status', [
                PlayerMailBroadcastStatus::QUEUED->value,
                PlayerMailBroadcastStatus::PROCESSING->value,
            ])
            ->update([
                'status' => PlayerMailBroadcastStatus::FAILED->value,
                'updated_at' => now(),
            ]);
    }
}
