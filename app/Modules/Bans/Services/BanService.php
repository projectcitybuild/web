<?php
namespace App\Modules\Bans\Services;

use App\Modules\Servers\Models\ServerKey;
use App\Modules\Bans\Models\{GameBan, GameUnban};
use App\Modules\Bans\Exceptions\{UnauthorisedKeyActionException, UserAlreadyBannedException, UserNotBannedException};
use App\Modules\Bans\Repositories\{GameBanRepository, GameUnbanRepository};
use Illuminate\Database\Connection;

class BanService {

    /**
     * @var GameBanRepository
     */
    private $banRepository;

    /**
     * @var GameUnbanRepository
     */
    private $unbanRepository;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(GameBanRepository $banRepository, GameUnbanRepository $unbanRepository, Connection $connection) {
        $this->banRepository = $banRepository;
        $this->unbanRepository = $unbanRepository;
        $this->connection = $connection;
    }
    
    /**
     * Stores a new ban
     *
     * @param ServerKey $key
     * @param int $playerGameUserId
     * @param int $staffGameUserId
     * @param string $reason
     * @param int $expiryTimestamp
     * @param bool $isGlobalBan
     * 
     * @throws UnauthorisedKeyActionException
     * @throws UserAlreadyBannedException
     * 
     * @return void
     */
    public function storeBan(ServerKey $key, int $playerGameUserId, int $staffGameUserId, string $reason, int $expiryTimestamp, bool $isGlobalBan) : GameBan {
        if($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('This key does not have permission to create a global ban');
        }
        if(!$isGlobalBan && !$serverKey->can_local_ban) {
            throw new UnauthorisedKeyActionException('This key does not have permission to create a local ban');
        }

        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        if(isset($existingBan)) {
            // TODO: update local ban with global if necessary

            throw new UserAlreadyBannedException('Player is already banned');
        }

        $ban = $this->banRepository->store([
            'server_id'             => $serverKey->server_id,
            'player_game_user_id'   => $playerGameUserId,
            'staff_game_user_id'    => $staffGameUserId,
            'reason'                => $reason,
            'is_active'             => true,
            'is_global_ban'         => $isGlobalBan,
            'expires_at'            => $expiryTimestamp ? Carbon::createFromTimestamp($expiryTimestamp) : null,
        ]);

        $serverKey->touch();

        // TODO: log usage of server key

        return $ban;
    }

    /**
     * Stores a new unban for the given player id's current ban
     *
     * @param ServerKey $key
     * @param int $playerGameUserId
     * @param int $staffGameUserId
     * @return GameUnban
     */
    public function storeUnban(ServerKey $key, int $playerGameUserId, int $staffGameUserId) : GameUnban {
        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        if(is_null($existingBan)) {
            throw new UserNotBannedException('This player is not currently banned');
        }

        if($existingBan->server_id == $serverKey->server_id) {
            // can remove local bans or global bans from this server?
            if(!$serverKey->can_local_ban) {
                throw new UnauthorisedKeyActionException('This key does not have permission to remove local bans');
            }
        } else {
            // can remove global ban from other servers?
            if($existingBan->is_global_ban && !$serverKey->can_global_ban) {
                throw new UnauthorisedKeyActionException('This key does not have permission to remove global bans');
            }
        }

        $unban = null;
        $this->connection->beginTransaction();
        try {
            $this->banRepository->deactivateBan($existingBan->game_ban_id);
            $unban = $this->unbanRepository->store($existingBan->game_ban_id, $staffGameUserId);

            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
        }

        $serverKey->touch();

        // TODO: log usage of server key

        return $unban;
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