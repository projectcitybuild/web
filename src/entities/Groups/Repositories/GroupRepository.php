<?php
namespace Entities\Groups\Repositories;

use Application\Repository;
use Entities\Groups\Models\Group;
use Illuminate\Support\Collection;

final class GroupRepository extends Repository {

    protected $model = Group::class;

    public function getGroupByName(String $groupName) : ?Group {
        return $this->getModel()
            ->where('name', $groupName)
            ->first();
    }

    public function getAll() : ?Collection {
        return $this->getModel()->all();
    }

}