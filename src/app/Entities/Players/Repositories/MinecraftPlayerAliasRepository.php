<?php
namespace App\Entities\Players\Repositories;

use App\Entities\Players\Models\MinecraftPlayerAlias;
use App\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MinecraftPlayerAliasRepository extends Repository
{
    protected $model = MinecraftPlayerAlias::class;

    /**
     * Creates a new MinecraftPlayerAlias tied to a
     * MinecraftPlayer id
     *
     * @param int $userId
     * @return GameUser
     */
    public function store(string $minecraftPlayerId,
                          string $alias,
                          Carbon $registeredAt = null) : MinecraftPlayerAlias 
    {
        return $this->getModel()->create([
            'player_minecraft_id'   => $minecraftPlayerId,
            'alias'                 => $alias,
            'registered_at'         => $registeredAt,
        ]);
    }

    public function getByAlias(string $alias) : ?MinecraftPlayerAlias
    {
        return $this->getModel()
            ->where('alias', $alias)
            ->first();
    }

    public function getAllByAlias(string $alias) : Collection
    {
        $results = $this->getModel()
            ->where('alias', $alias)
            ->get();

        return $results ?: collect();
    }
}
