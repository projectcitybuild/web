<?php

namespace App\Domains\Players\Jobs;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchPlayerAliasJob implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public MinecraftPlayer $player,
    ) {}

    public function handle(LookupMinecraftUUID $lookup)
    {
        $result = $lookup->fetch(uuid: new MinecraftUUID($this->player->uuid));
        $context = [
            'result' => $result,
            'player' => $this->player,
        ];
        if ($result === null || $result->username === '') {
            Log::warning('Username from lookup was empty or null', $context);
            return;
        }
        Log::info('Fetched missing alias for player', $context);
        $this->player->alias = $result->username;
        $this->player->save();
    }
}
