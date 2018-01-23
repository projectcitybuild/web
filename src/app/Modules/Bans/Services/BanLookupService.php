<?php
namespace App\Modules\Bans\Services;

use App\Modules\Servers\Models\ServerKey;
use App\Modules\Bans\Exceptions\{UnauthorisedKeyActionException, UserAlreadyBannedException, UserNotBannedException};
use App\Modules\Bans\Repositories\GameBanRepository;
use Illuminate\Database\Connection;

class BanLookupService {

    /**
     * @var GameBanRepository
     */
    private $banRepository;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        GameBanRepository $banRepository, 
        Connection $connection
    ) {
        $this->banRepository = $banRepository;
        $this->connection = $connection;
    }
    
    /**
     * Returns the first active ban that belongs to the given player.
     * Otherwise returns null if no active ban
     *
     * @param ServerKey $key
     * @param int $playerGameUserId
     * @return GameBan|null
     */
    public function getActivePlayerBan(ServerKey $key, int $playerGameUserId) : ?GameBan {
        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);

        $serverKey->touch();

        return $existingBan;
    }

    public function getPlayerBanHistory(int $playerGameUserId, int $serverId = null) {
        
    }

}