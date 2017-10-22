<?php
namespace App\Modules\Users\Repositories;

use App\Modules\Users\Models\UserAlias;

class UserAliasRepository {

    /**
     * @var UserAlias
     */
    private $aliasModel;

    public function __construct(UserAlias $aliasModel) {
        $this->aliasModel = $aliasModel;
    }

    /**
     * Gets an alias by alias type id and alias
     *
     * @param int $aliasType
     * @param string $name
     * @return UserAlias|null
     */
    public function getAlias(int $aliasType, string $name) : ?UserAlias {
        return $this->aliasModel
            ->where('user_alias_type_id', $aliasType)
            ->where('alias', $name)
            ->first();
    }

    /**
     * Creates a new UserAlias
     *
     * @param int $aliasType
     * @param int $gameUserId
     * @param string $alias
     * @return UserAlias
     */
    public function store(int $aliasType, int $gameUserId, string $alias) : UserAlias {
        return $this->aliasModel->create([
            'user_alias_type_id' => $aliasType,
            'game_user_id' => $gameUserId,
            'alias' => $alias,
        ]);
    }

    public function getAliasesByIds(array $aliasIds) {
        return $this->aliasModel
            ->whereIn('user_alias_id', $aliasIds)
            ->get();
    }

}