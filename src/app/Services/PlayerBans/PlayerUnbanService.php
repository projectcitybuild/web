<?php
namespace Domains\Services\PlayerBans;

use Entities\Bans\Repositories\GameUnbanRepository;
use Entities\Bans\Models\GameUnban;
use Entities\GamePlayerType;
use Domains\Services\PlayerLookup\PlayerLookupService;
use Domains\Services\PlayerBans\Exceptions\UserNotBannedException;
use Entities\Bans\Repositories\GameBanRepository;
use Entities\GameTypeEnum;
use Illuminate\Database\Connection;


class PlayerUnbanService
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