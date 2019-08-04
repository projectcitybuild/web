<?php

namespace App\Http\Actions\GameBans;

use App\Entities\GamePlayerType;
use App\Http\Actions\Players\GetOrCreatePlayer;
use App\Entities\Bans\Models\GameUnban;
use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use Illuminate\Support\Facades\DB;

final class CreatePlayerUnban
{
    private $getOrCreatePlayer;
    private $getPlayerBans;

    public function __construct(GetOrCreatePlayer $getOrCreatePlayer, GetPlayerBans $getPlayerBans)
    {
        $this->getOrCreatePlayer = $getOrCreatePlayer;
        $this->getPlayerBans = $getPlayerBans;
    }

    public function execute(
        string $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        string $staffPlayerId,
        GamePlayerType $staffPlayerType
    ) : GameUnban {

        $bannedPlayer = $this->getOrCreatePlayer->execute(
            $bannedPlayerType, 
            $bannedPlayerId
        );
        
        $staffPlayer  = $this->getOrCreatePlayer->execute(
            $staffPlayerType, 
            $staffPlayerId
        );
        
        $getOnlyFirstResult = true;
        $activeBan = $this->getPlayerBans->execute(
            $getOnlyFirstResult,
            $bannedPlayer->getKey(), 
            $bannedPlayerType
        );

        if ($activeBan === null) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        DB::beginTransaction();
        try {
            $activeBan->is_active = false;
            $activeBan->save();
    
            $unban = GameUnban::create([
                'game_ban_id'           => $activeBan->getKey(),
                'staff_player_id'       => $staffPlayer->getKey(),
                'staff_player_type'     => $staffPlayerType->valueOf(),
            ]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $unban;
    }
}