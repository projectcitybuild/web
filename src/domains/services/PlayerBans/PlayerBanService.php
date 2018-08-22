<?php
namespace Domains\Services\PlayerBans;

use Domains\Modules\Bans\Repositories\GameBanRepository;
use Domains\Modules\Bans\Models\GameBan;
use Domains\Modules\GamePlayerType;
use Domains\Services\PlayerLookup\PlayerLookupService;
use Domains\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use Domains\GameTypeEnum;


class PlayerBanService
{
    /**
     * @var GameBanRepository
     */
    private $gameBanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;


    public function __construct(GameBanRepository $gameBanRepository,
                                PlayerLookupService $playerLookupService)
    {
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
    }


    public function ban(string $bannedPlayerId,
                        GamePlayerType $bannedPlayerType,
                        string $bannedPlayerAlias,
                        string $staffPlayerId,
                        GamePlayerType $staffPlayerType,
                        ?string $banReason,
                        ?int $banExpiresAt = null,
                        bool $isGlobalBan = false) : GameBan
    {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
        $staffPlayer  = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
        
        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType);
        if ($activeBan !== null) {
            throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
        }

        $serverId = 1;

        return $this->gameBanRepository->store($serverId,
                                               $bannedPlayer->getKey(),
                                               $bannedPlayerType,
                                               $bannedPlayerAlias,
                                               $staffPlayer->getKey(),
                                               $staffPlayerType,
                                               $banReason,
                                               true,
                                               $isGlobalBan,
                                               $banExpiresAt ? Carbon::createFromTimestamp($banExpiresAt) : null);
    }
}