<?php
namespace App\Modules\Bans\Services;

use App\Modules\Bans\Exceptions\UserAlreadyBannedException;
use App\Modules\Bans\Exceptions\UserNotBannedException;
use App\Modules\Bans\Repositories\GameBanRepository;
use App\Modules\ServerKeys\Models\ServerKey;
use App\Modules\Bans\Models\GameBan;

class BanLookupService {

    /**
     * @var GameBanRepository
     */
    private $banRepository;

    
    public function __construct(GameBanRepository $banRepository) {
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
    public function getActivePlayerBan(int $bannedPlayerId, string $bannedPlayerType, ServerKey $serverKey) : ?GameBan {
        $existingBan = $this->banRepository->getActiveBanByGameUserId($bannedPlayerId, $bannedPlayerType, $serverKey->server_id);
        return $existingBan;
    }

    public function getPlayerBanHistory(int $playerGameUserId, int $serverId = null) {
        
    }

}