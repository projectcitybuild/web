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
     * @param int $banId
     * @param int $staffGameUserId
     * @return GameUnban
     */
    public function store(int $banId, int $staffGameUserId) : GameUnban {
        return $this->unbanModel->create([
            'game_ban_id'           => $banId,
            'staff_game_user_id'    => $staffGameUserId,
        ]);
    }

}