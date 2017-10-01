<?php
namespace App\Modules\Bans\Repositories;

use App\Modules\Bans\Models\GameBan;

class GameBanRepository {

    private $banModel;

    public function __construct(GameBan $banModel) {
        $this->banModel = $banModel;
    }

    /**
     * Stores a new GameBan
     *
     * @param array $input
     * @return GameBan
     */
    public function store(array $input) : GameBan {
        return $this->banModel->create($input);
    }

    /**
     * Gets the first active ban for the given game user id.
     * If a server id is not specified, searches only for global bans
     *
     * @param int $gameUserId
     * @param int $serverId
     * @return GameBan|null
     */
    public function getActiveBanByGameUserId(int $gameUserId, int $serverId = null) : ?GameBan {
        return $this->banModel
            ->where('player_game_user_id', $gameUserId)
            ->where('is_active', true)
            ->when(isset($serverId), function($q) use($serverId) {
                return $q->where('server_id', $serverId)
                       ->orWhere('is_global_ban', true);
            })
            ->when(is_null($serverId), function($q) {
                return $q->where('is_global_ban', true);
            })
            ->first();
    }

    /**
     * Sets the given ban as inactive
     *
     * @param int $banId
     * @return void
     */
    public function deactivateBan(int $banId) {
        return $this->banModel
            ->where('game_ban_id', $banId)
            ->update(['is_active' => false]);
    }

}