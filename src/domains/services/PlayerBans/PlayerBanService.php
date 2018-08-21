<?php
namespace Domains\Services\PlayerBans;

use Domains\Modules\Bans\Repositories\GameBanRepository;
use Domains\Modules\Bans\Models\GameBan;
use Domains\Modules\GameIdentifierType;
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
                        GameIdentifierType $bannedPlayerType,
                        string $bannedPlayerAlias,
                        string $staffPlayerId,
                        GameIdentifierType $staffPlayerType,
                        ?string $banReason,
                        ?int $banExpiresAt = null,
                        bool $isGlobalBan = false) : GameBan
    {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
        $staffPlayer  = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
        
        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType->playerType());
        if ($activeBan !== null) {
            throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
        }

        return $this->gameBanRepository->store($serverId,
                                               $bannedPlayer->getKey(),
                                               $bannedPlayerType->playerType(),
                                               $bannedPlayerAlias,
                                               $staffPlayerId,
                                               $staffPlayerType->playerType(),
                                               $banReason,
                                               true,
                                               $isGlobalBan,
                                               $banExpiresAt ? Carbon::createFromTimestamp($banExpiresAt) : null);
    }
}