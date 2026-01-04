<?php

namespace App\Domains\MinecraftStats\Jobs;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Models\MinecraftPlayer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementPlayerStatsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MinecraftUUID $uuid,
        public PlayerStatIncrement $increment,
    ) {}

    public function handle()
    {
        // TODO: schedule another job to fetch alias later
        $player = MinecraftPlayer::firstOrCreate($this->uuid);

        // Note: increment() for built-in atomic safety
        $player->increment('afk_time', $this->increment->afkTime);
        $player->increment('blocks_placed', $this->increment->blocksPlaced);
        $player->increment('blocks_destroyed', $this->increment->blocksDestroyed);
        $player->increment('blocks_travelled', $this->increment->blocksTravelled);
        $player->save();
    }
}
