<?php
namespace App\Modules\Bans\Repositories;

use App\Modules\Bans\Models\GameBan;
use App\Support\Repository;
use Illuminate\Database\Eloquent\Collection;

class GameBanRepository extends Repository {

    protected $model = GameBan::class;

    /**
     * Stores a new GameBan
     *
     * @param integer $serverId
     * @param integer $bannedPlayerId
     * @param string $bannedPlayerType
     * @param string $bannedAliasAtTime
     * @param integer $staffPlayerId
     * @param string $staffPlayerType
     * @param string|null $reason
     * @param boolean $isActive
     * @param boolean $isGlobalBan
     * @param integer|null $expiresAt
     * 
     * @return GameBan
     */
    public function store(
        int $serverId,
        int $bannedPlayerId,
        string $bannedPlayerType,
        string $bannedAliasAtTime,
        int $staffPlayerId,
        string $staffPlayerType,
        ?string $reason = null,
        bool $isActive = true,
        bool $isGlobalBan = false,
        ?int $expiresAt = null
    ) : GameBan {
        
        return $this->getModel()->create([
            'server_id'             => $serverId,
            'banned_player_id'      => $bannedPlayerId,
            'banned_player_type'    => $bannedPlayerType,
            'banned_alias_at_time'  => $bannedAliasAtTime,
            'staff_player_id'       => $staffPlayerId,
            'staff_player_type'     => $staffPlayerType,
            'reason'                => $reason,
            'is_active'             => $isActive,
            'is_global_ban'         => $isGlobalBan,
            'expires_at'            => $expiresAt,
        ]);
    }

    /**
     * Gets the first active ban for the given game user id.
     * If a server id is not specified, searches only for global bans
     *
     * @param int $bannedPlayerId
     * @param string $bannedPlayerType
     * @param int $serverId
     * @return GameBan|null
     */
    public function getActiveBanByGameUserId(
        int $bannedPlayerId,
        string $bannedPlayerType,
        int $serverId = null,
        array $with = []
    ) : ?GameBan {
    
        return $this->getModel()
            ->with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType)
            ->where('is_active', true)
            ->when(isset($serverId), 
                function($q) use($serverId) {
                    return $q->where('server_id', $serverId)
                           ->orWhere('is_global_ban', true);
                },
                function($q) {
                    return $q->where('is_global_ban', true);
                }
            )
            ->first();
    }

    /**
     * Sets the given ban as inactive
     *
     * @param int $banId
     * @return void
     */
    public function deactivateBan(int $banId) {
        return $this->getModel()
            ->where('game_ban_id', $banId)
            ->update([
                'is_active' => false,
            ]);
    }

    /**
     * Returns a collection of GameBans
     *
     * @param int $take
     * @param int $offset
     * @param array $sort
     * @param array $filter
     * @return void
     */
    public function getBans(int $take = 50, int $offset = 0, array $sort = null, array $filter = []) {
        return $this->getModel()
            ->when(count($filter) > 0, function($q) use($filter) {
                if(array_key_exists('player_alias_at_ban', $filter)) {
                    $q->where('player_alias_at_ban', 'LIKE', $filter['player_alias_at_ban'] . '%');
                }
                if(array_key_exists('is_active', $filter)) {
                    $q->where('is_active', $filter['is_active']);
                }
                return $q;
            })
            ->when(array_key_exists('banned_alias', $filter), function($q) use($filter) {
                return $q->whereHas('bannedAlias', function($sq) use($filter) {
                    $sq->where('alias', $filter['banned_alias']);
                });
            })
            ->when(isset($sort), function($q) use($sort) {
                return $q->orderBy($sort['field'], $sort['order']);
            })
            ->when(is_null($sort), function($q) {
                return $q->orderBy('game_ban_id', 'DESC');
            })
            ->take($take)
            ->skip($offset)
            ->get();
    }

    public function getBanCount() {
        return $this->getModel()->count();
    }

    public function getActiveExpiredBans() {
        return $this->getModel()
            ->where('is_active', true)
            ->whereDate(Carbon::now(), '>=', 'expires_at')
            ->get();
    }

    public function getBansByPlayer(int $bannedPlayerId, string $bannedPlayerType, array $with = []) : ?Collection {
        return $this->getModel()
            ->with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType)
            ->get();
    }
}