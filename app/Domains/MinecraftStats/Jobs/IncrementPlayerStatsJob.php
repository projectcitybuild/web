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
        private MinecraftUUID $uuid,
        private PlayerStatIncrement $increment,
    ) {}

    public function handle()
    {
        // TODO: schedule another job to fetch alias later
        $player = MinecraftPlayer::firstOrCreate($this->uuid);

        $player->afk_time += max(0, $this->increment->afkTime);
        $player->blocks_placed += max(0, $this->increment->blocksPlaced);
        $player->blocks_destroyed += max(0, $this->increment->blocksDestroyed);
        $player->blocks_travelled += max(0, $this->increment->blocksTravelled);
        $player->save();
    }
}
