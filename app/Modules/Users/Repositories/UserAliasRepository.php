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

    public function getAlias(int $typeId, string $name) : ?UserAlias {
        return $this->aliasModel
            ->where('user_alias_type_id', $typeId)
            ->where('alias', $name)
            ->first();
    }

}