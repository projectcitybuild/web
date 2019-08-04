<?php

namespace App\Http\Actions\Players;

use App\Entities\GamePlayerType;
use App\Entities\Players\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;

final class GetOrCreatePlayer
{
    public function execute(GamePlayerType $playerType, string $identifier) {
         return $this->getByUuid($playerType, $identifier) 
             ?: $this->store($playerType, $identifier);
    }

    private function getByUuid(GamePlayerType $playerType, string $identifier) {
        switch ($playerType) {
            case GamePlayerType::Minecraft:
                return MinecraftPlayer::where('uuid', $identifier)->first();
        }
    }

    private function store(GamePlayerType $playerType, string $identifier) {
        switch ($playerType) {
            case GamePlayerType::Minecraft:
                return MinecraftPlayer::create([
                    'uuid'          => $identifier,
                    'account_id'    => null,
                    'playtime'      => 0,
                    'last_seen_at'  => Carbon::now(),
                ]);
        }
    }
}