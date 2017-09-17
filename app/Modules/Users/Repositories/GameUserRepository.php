<?php
namespace App\Modules\Users\Repositories;

use App\Modules\Users\Models\GameUser;

class GameUserRepository {

    private $gameUserModel;

    public function __construct(GameUser $gameUserModel) {
        $this->gameUserModel = $gameUserModel;
    }

    public function getGameUserByAlias($aliasTypeId, $alias) {

    }

}