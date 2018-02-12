<?php
namespace App\Modules\Bans\Services;

use App\Modules\Bans\Models\GameBan;
use App\Modules\Bans\Models\GameUnban;
use App\Modules\Bans\Exceptions\UserAlreadyBannedException;
use App\Modules\Bans\Exceptions\UserNotBannedException;
use App\Modules\Bans\Repositories\GameBanRepository;
use App\Modules\Bans\Repositories\GameUnbanRepository;

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
        GameUnbanRepository $unbanRepository
    ) {
        $this->banRepository = $banRepository;
        $this->unbanRepository = $unbanRepository;
    }
    
    /**
     * Stores a new ban
     *
     * @param int $serverId             Server the player was on at the time of ban.
     * @param int $playerGameUserId     game_user_id of the player to be banned.
     * @param int $staffGameUserId      game_user_id of the staff who is banning the player.
     * @param int $bannedAliasId        The alias_id that will be banned from the server
     * @param string $aliasAtBan        The human-readable alias of the player at the time of ban.
     *                                  For example, their Minecraft in-game name (not their uuid)
     * @param string $reason            Reason for ban set by the staff member
     * @param int $expiryTimestamp      If set, the timestamp of when the ban auto expires.
     * @param bool $isGlobalBan         If true, prevents the user accessing all PCB services.
     * 
     * @throws UserAlreadyBannedException
     * 
     * @return void
     */
    public function storeBan(
        int $serverId,
        int $bannedPlayerId,
        string $bannedPlayerType,
        string $bannedAliasAtTime,
        int $staffPlayerId, 
        string $staffPlayerType,
        ?string $reason = null, 
        ?int $expiryTimestamp = null,
        bool $isGlobalBan = false
    ) : GameBan {

        $existingBan = $this->banRepository->getActiveBanByGameUserId($bannedPlayerId, $bannedPlayerType, $serverId);
        if(isset($existingBan)) {
            // a player should only ever have one active ban, so prevent creating
            // the same local ban twice
            if(!$isGlobalBan) {
                throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
            }

            // however, if we want to globally ban a user who is locally banned, 
            // deactivate it first so we can then create a global ban, otherwise
            // it would be harder to track who originally banned the player
            $this->banRepository->deactivateBan($existingBan->game_ban_id);
        }

        return $this->banRepository->store(
            $serverId,
            $bannedPlayerId,
            $bannedPlayerType,
            $bannedAliasAtTime,
            $staffPlayerId,
            $staffPlayerType,
            $reason,
            true,
            $isGlobalBan,
            $expiryTimestamp ? Carbon::createFromTimestamp($expiryTimestamp) : null
        );
    }

    /**
     * Stores a new unban for the given player id's current ban
     *
     * @param int $serverId             Server to unban the player on (used only for local bans).
     * @param int $playerGameUserId     game_user_id of the player to unban.
     * @param int $staffGameUserId      game_user_id of the staff who is unbanned the player.
     * 
     * @throws UserNotBannedException
     * 
     * @return GameUnban
     */
    public function storeUnban(int $serverId, int $playerGameUserId, int $staffGameUserId) : GameUnban {
        $existingBan = $this->banRepository->getActiveBanByGameUserId($playerGameUserId, $serverId);
        
        // can't unban a player who isn't banned
        if(is_null($existingBan)) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        $this->banRepository->deactivateBan($existingBan->game_ban_id);
        $unban = $this->unbanRepository->store($existingBan->game_ban_id, $staffGameUserId);

        return $unban;
    }

}