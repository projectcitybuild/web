<?php
namespace App\Modules\Players\Repositories;

use App\Modules\Players\Models\Player;

class PlayerRepository {

    private $model;

    public function __construct(Player $model) {
        $this->model = $model;
    }

    /**
     * Creates a new GameUser
     *
     * @param int $userId
     * @return GameUser
     */
    public function store($userId = null) : Player {
        return $this->model->create([
            'user_id' => $userId,
        ]);
    }

}