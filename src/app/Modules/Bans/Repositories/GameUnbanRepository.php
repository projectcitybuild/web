<?php
namespace App\Modules\Bans\Repositories;

use App\Modules\Bans\Models\GameUnban;

class GameUnbanRepository {

    private $unbanModel;

    public function __construct(GameUnban $unbanModel) {
        $this->unbanModel = $unbanModel;
    }

    /**
     * Stores a new GameUnban
     *
     * @param integer $banId
     * @param integer $staffPlayerId
     * @param string $staffPlayerType
     *
     * @return GameUnban
     */
    public function store(int $banId, int $staffPlayerId, string $staffPlayerType) : GameUnban {
        return $this->unbanModel->create([
            'game_ban_id'           => $banId,
            'staff_player_id'       => $staffPlayerId,
            'staff_player_type'     => $staffPlayerType,
        ]);
    }

}