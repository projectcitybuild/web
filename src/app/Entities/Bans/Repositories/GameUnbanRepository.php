<?php
namespace App\Entities\Bans\Repositories;

use Application\Contracts\Repository;
use App\Entities\Bans\Models\GameUnban;

class GameUnbanRepository extends Repository
{
    protected $model = GameUnban::class;

    /**
     * Stores a new GameUnban
     *
     * @param integer $banId
     * @param integer $staffPlayerId
     * @param string $staffPlayerType
     *
     * @return GameUnban
     */
    public function store(int $banId, int $staffPlayerId, string $staffPlayerType) : GameUnban
    {
        return $this->getModel()->create([
            'game_ban_id'           => $banId,
            'staff_player_id'       => $staffPlayerId,
            'staff_player_type'     => $staffPlayerType,
        ]);
    }
}
