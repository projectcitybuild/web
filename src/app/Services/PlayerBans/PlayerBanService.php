<?php

namespace App\Services\PlayerBans;

use App\Entities\Bans\Repositories\GameBanRepository;
use App\Entities\Bans\Models\GameBan;
use App\Entities\GamePlayerType;
use App\Services\PlayerLookup\PlayerLookupService;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Entities\Bans\Repositories\GameUnbanRepository;
use App\Entities\Bans\Models\GameUnban;
use Illuminate\Support\Facades\DB;
use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use Carbon\Carbon;

final class PlayerBanService
{
    /**
     * @var GameBanRepository
     */
    private $gameBanRepository;

    /**
     * @var GameUnbanRepository
     */
    private $gameUnbanRepository;
    
    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;


    public function __construct(
        GameBanRepository $gameBanRepository,
        GameUnbanRepository $gameUnbanRepository,
        PlayerLookupService $playerLookupService
    ) {
        $this->gameBanRepository = $gameBanRepository;
        $this->gameUnbanRepository = $gameUnbanRepository;
        $this->playerLookupService = $playerLookupService;
    }

    public function unban(
        string $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        string $staffPlayerId,
        GamePlayerType $staffPlayerType
    ) : GameUnban {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
        $staffPlayer  = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
        
        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType);
        if ($activeBan === null) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        DB::beginTransaction();
        try {
            $activeBan->is_active = false;
            $activeBan->save();
    
            $unban = $this->gameUnbanRepository->store(
                $activeBan->getKey(),
                $staffPlayer->getKey(),
                $staffPlayerType
            );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $unban;
    }
}