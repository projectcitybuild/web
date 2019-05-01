<?php

namespace App\Services\PlayerBans;

use App\Entities\Bans\Repositories\GameUnbanRepository;
use App\Entities\Bans\Models\GameUnban;
use App\Entities\GamePlayerType;
use App\Services\PlayerLookup\PlayerLookupService;
use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use App\Entities\Bans\Repositories\GameBanRepository;
use App\Entities\GameType;
use Illuminate\Database\Connection;

final class PlayerUnbanService
{
    /**
     * @var GameUnbanRepository
     */
    private $gameUnbanRepository;

    /**
     * @var GameBanRepository
     */
    private $gameBanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(GameBanRepository $gameBanRepository,
                                GameUnbanRepository $gameUnbanRepository,
                                PlayerLookupService $playerLookupService,
                                Connection $connection)
    {
        $this->gameUnbanRepository = $gameUnbanRepository;
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
        $this->connection = $connection;
    }


    public function unban(string $bannedPlayerId,
                          GamePlayerType $bannedPlayerType,
                          string $staffPlayerId,
                          GamePlayerType $staffPlayerType) : GameUnban
    {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
        $staffPlayer  = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
        
        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType);
        if ($activeBan === null) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        $this->connection->beginTransaction();
        try {
            $activeBan->is_active = false;
            $activeBan->save();
    
            $unban = $this->gameUnbanRepository->store($activeBan->getKey(),
                                                       $staffPlayer->getKey(),
                                                       $staffPlayerType);
            $this->connection->commit();
            return $unban;

        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}