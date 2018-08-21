<?php
namespace Domains\Services\PlayerBans;

use Domains\Modules\Bans\Repositories\GameUnbanRepository;
use Domains\Modules\Bans\Models\GameUnban;
use Domains\Modules\GameIdentifierType;
use Domains\Services\PlayerLookup\PlayerLookupService;
use Domains\Services\PlayerBans\Exceptions\UserNotBannedException;
use Domains\GameTypeEnum;
use Domains\Modules\Bans\Repositories\GameBanRepository;


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


    public function __construct(GameBanRepository $gameBanRepository,
                                GameUnbanRepository $gameUnbanRepository,
                                PlayerLookupService $playerLookupService)
    {
        $this->gameUnbanRepository = $gameUnbanRepository;
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
    }


    public function unban(string $bannedPlayerId,
                          GameIdentifierType $bannedPlayerType,
                          string $staffPlayerId,
                          GameIdentifierType $staffPlayerType) : GameUnban
    {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
        $staffPlayer  = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
        
        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType->playerType());
        if ($activeBan === null) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        return $this->gameUnbanRepository->store($activeBan->getKey(),
                                                 $staffPlayerId,
                                                 $staffPlayerType->playerType());
    }
}