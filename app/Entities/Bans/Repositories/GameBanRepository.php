<?php

namespace App\Entities\Bans\Repositories;

use App\Entities\Bans\Models\GameBan;
use App\Entities\GamePlayerType;
use App\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @deprecated Use GameBan model facade instead
 */
final class GameBanRepository extends Repository
{
    protected $model = GameBan::class;

    /**
     * Stores a new GameBan.
     *
     * @param string $bannedPlayerType
     * @param string $staffPlayerType
     */
    public function store(
        int $serverId,
        int $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        string $bannedAliasAtTime,
        int $staffPlayerId,
        GamePlayerType $staffPlayerType,
        ?string $reason = null,
        bool $isActive = true,
        bool $isGlobalBan = false,
        ?int $expiresAt = null): GameBan
    {
        return $this->getModel()->create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_player_type' => $bannedPlayerType->valueOf(),
            'banned_alias_at_time' => $bannedAliasAtTime,
            'staff_player_id' => $staffPlayerId,
            'staff_player_type' => $staffPlayerType->valueOf(),
            'reason' => $reason,
            'is_active' => $isActive,
            'is_global_ban' => $isGlobalBan,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Gets the first active ban for the given game user id.
     * If a server id is not specified, searches only for global bans.
     *
     * @param string $bannedPlayerType
     * @param int $serverId
     */
    public function getActiveBanByGameUserId(
        int $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        ?int $serverId = null,
        array $with = []): ?GameBan
    {
        return $this->getModel()
            ->with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType->valueOf())
            ->where('is_active', true)
            ->when(isset($serverId),
                function ($q) use ($serverId) {
                    return $q->where('server_id', $serverId)
                        ->orWhere('is_global_ban', true);
                },
                function ($q) {
                    return $q->where('is_global_ban', true);
                }
            )
            ->first();
    }

    /**
     * Sets the given ban as inactive.
     *
     *
     * @return void
     */
    public function deactivateBan(int $banId)
    {
        return $this->getModel()
            ->where('game_ban_id', $banId)
            ->update([
                'is_active' => false,
            ]);
    }

    /**
     * Returns a collection of GameBans.
     *
     * @param array $sort
     *
     * @return void
     */
    public function getBans(int $take = 50, int $offset = 0, ?array $sort = null, array $filter = [])
    {
        return $this->getModel()
            ->when(count($filter) > 0, function ($q) use ($filter) {
                if (array_key_exists('player_alias_at_ban', $filter)) {
                    $q->where('player_alias_at_ban', 'LIKE', $filter['player_alias_at_ban'].'%');
                }
                if (array_key_exists('is_active', $filter)) {
                    $q->where('is_active', $filter['is_active']);
                }

                return $q;
            })
            ->when(array_key_exists('banned_alias', $filter), function ($q) use ($filter) {
                return $q->whereHas('bannedAlias', function ($sq) use ($filter) {
                    $sq->where('alias', $filter['banned_alias']);
                });
            })
            ->when(isset($sort), function ($q) use ($sort) {
                return $q->orderBy($sort['field'], $sort['order']);
            })
            ->when(is_null($sort), function ($q) {
                return $q->orderBy('game_ban_id', 'DESC');
            })
            ->take($take)
            ->skip($offset)
            ->get();
    }

    public function getBanCount()
    {
        return $this->getModel()->count();
    }

    public function getActiveExpiredBans()
    {
        return $this->getModel()
            ->where('is_active', true)
            ->whereDate(Carbon::now(), '>=', 'expires_at')
            ->get();
    }

    public function getBansByPlayer(int $bannedPlayerId, string $bannedPlayerType, array $with = []): ?Collection
    {
        return $this->getModel()
            ->with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType)
            ->get();
    }
}
