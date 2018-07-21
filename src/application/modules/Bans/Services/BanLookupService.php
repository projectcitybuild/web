<?php
namespace Application\Modules\Bans\Services;

use Application\Modules\Bans\Exceptions\UserAlreadyBannedException;
use Application\Modules\Bans\Exceptions\UserNotBannedException;
use Application\Modules\Bans\Repositories\GameBanRepository;
use Application\Modules\ServerKeys\Models\ServerKey;
use Application\Modules\Bans\Models\GameBan;
use Illuminate\Database\Eloquent\Collection;

class BanLookupService
{

    /**
     * @var GameBanRepository
     */
    private $banRepository;

    
    public function __construct(GameBanRepository $banRepository)
    {
        $this->banRepository = $banRepository;
    }
    
    /**
     * Returns the first active ban that belongs to the given player.
     * Otherwise returns null if no active ban
     *
     * @param ServerKey $key
     * @param int $playerGameUserId
     * @return GameBan|null
     */
    public function getActivePlayerBan(
        int $bannedPlayerId,
        string $bannedPlayerType,
        ServerKey $serverKey,
        array $with = []
    ) : ?GameBan {
        return $this->banRepository->getActiveBanByGameUserId(
            $bannedPlayerId,
            $bannedPlayerType,
            $serverKey->server_id,
            $with
        );
    }

    public function getPlayerBanHistory(
        int $bannedPlayerId,
        string $bannedPlayerType,
        ServerKey $serverKey
    ) : ?Collection {
        return $this->banRepository->getBansByPlayer($bannedPlayerId, $bannedPlayerType, ['unban']);
    }
}
