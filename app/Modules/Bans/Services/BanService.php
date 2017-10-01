<?php
namespace App\Modules\Bans\Services;

use App\Modules\Servers\Models\ServerKey;
use App\Modules\Bans\Models\GameBan;
use App\Modules\Bans\Exceptions\UnauthorisedKeyActionException;
use App\Modules\Bans\Exceptions\UserAlreadyBannedException;

class BanService {

    private $banRepository;

    public function __construct(GameBanRepository $banRepository) {
        $this->banRepository = $banRepository;
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
     * @throws UnauthorisedKeyActionException
     * @throws UserAlreadyBannedException
     * @return void
     */
    public function storeBan(ServerKey $key, int $playerGameUserId, int $staffGameUserId, string $reason, int $expiryTimestamp, bool $isGlobalBan) : GameBan {
        if($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('This key does not have permission to create a global ban');
        }

        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverKey->server_id);
        if(isset($existingBan)) {
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

}