<?php
namespace App\Modules\Users\Repositories;

use App\Modules\Users\Models\{UserAlias, UserAliasType};

class UserAliasRepository {

    private $aliasModel;
    private $aliasTypeModel;

    public function __construct(UserAlias $aliasModel, UserAliasType $aliasTypeModel) {
        $this->aliasModel = $aliasModel;
        $this->aliasTypeModel = $aliasTypeModel;
    }

    /**
     * Gets an alias type by its name
     *
     * @param string $typeName
     * @return UserAliasType|null
     */
    public function getAliasType(string $typeName) : ?UserAliasType {
        return $this->aliasTypeModel
            ->where('name', $typeName)
            ->first();
    }

    /**
     * Gets an alias by alias type id and alias
     *
     * @param int $typeId
     * @param string $name
     * @return UserAlias|null
     */
    public function getAlias(int $typeId, string $name) : ?UserAlias {
        return $this->aliasModel
            ->where('user_alias_type_id', $typeId)
            ->where('alias', $name)
            ->first();
    }

    /**
     * Creates a new UserAlias
     *
     * @param int $aliasTypeId
     * @param int $gameUserId
     * @param string $alias
     * @return UserAlias
     */
    public function store(int $aliasTypeId, int $gameUserId, string $alias) : UserAlias {
        return $this->aliasModel->create([
            'user_alias_type_id' => $aliasTypeId,
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