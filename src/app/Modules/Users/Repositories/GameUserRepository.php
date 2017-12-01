<?php
namespace App\Modules\Users\Repositories;

use App\Modules\Users\Models\GameUser;

class GameUserRepository {

    private $gameUserModel;

    public function __construct(GameUser $gameUserModel) {
        $this->gameUserModel = $gameUserModel;
    }

    /**
     * Creates a new GameUser
     *
     * @param int $userId
     * @return GameUser
     */
    public function store($userId = null) : GameUser {
        return $this->gameUserModel->create([
            'user_id' => $userId,
        ]);
    }

}