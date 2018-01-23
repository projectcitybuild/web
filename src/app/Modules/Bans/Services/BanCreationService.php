<?php
namespace App\Modules\Bans\Services;

use App\Modules\Servers\Models\ServerKey;
use App\Modules\Bans\Models\{GameBan, GameUnban};
use App\Modules\Bans\Exceptions\{UnauthorisedKeyActionException, UserAlreadyBannedException, UserNotBannedException};
use App\Modules\Bans\Repositories\{GameBanRepository, GameUnbanRepository};
use Illuminate\Database\Connection;

class BanCreationService {

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

    public function __construct(
        GameBanRepository $banRepository, 
        GameUnbanRepository $unbanRepository,
        Connection $connection
    ) {
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
    public function storeBan(
        ServerKey $key, 
        int $playerGameUserId,
        int $staffGameUserId, 
        string $reason, 
        int $expiryTimestamp,
        bool $isGlobalBan
    ) : GameBan {

        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        if($existingBan !== null) {
            // TODO: update local ban with global if necessary

            throw new UserAlreadyBannedException('Player is already banned');
        }

        $this->connection->beginTransaction();
        try {
            $ban = $this->banRepository->store(
                $serverKey->server_id,
                $playerGameUserId,
                $staffGameUserId,
                $reason,
                true,
                $isGlobalBan,
                $expiryTimestamp ? Carbon::createFromTimestamp($expiryTimestamp) : null
            );
    
            $serverKey->touch();
    
            // TODO: log usage of server key
    
            $this->connection->commit();
            return $ban;
        
        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
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

        $this->banAuthService->validateUnban($existingBan, $serverKey);

        $this->connection->beginTransaction();
        try {
            $this->banRepository->deactivateBan($existingBan->game_ban_id);
            $unban = $this->unbanRepository->store($existingBan->game_ban_id, $staffGameUserId);
            $serverKey->touch();

            // TODO: log usage of server key
        
            $this->connection->commit();
            return $unban;

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
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