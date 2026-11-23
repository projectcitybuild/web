<?php

namespace App\Domains\PlayerMail\Jobs;

use App\Core\Data\JobQueue;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Models\PlayerMailBroadcast;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class RecoverPlayerMailBroadcastsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 3600;

    public function __construct()
    {
        $this->onQueue(JobQueue::PLAYER_MAIL->value);
    }

    public function handle(): void
    {
        PlayerMailBroadcast::query()
            ->whereIn('status', [
                PlayerMailBroadcastStatus::QUEUED->value,
                PlayerMailBroadcastStatus::PROCESSING->value,
            ])
            ->where('updated_at', '<=', now()->subMinutes(5))
            ->orderBy('id')
            ->chunkById(100, function (Collection $broadcasts): void {
                foreach ($broadcasts as $broadcast) {
                    DistributePlayerMailJob::dispatch($broadcast->id, $broadcast->last_player_id);
                }
            });
    }
}
