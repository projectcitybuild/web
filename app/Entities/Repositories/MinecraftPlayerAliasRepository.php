<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\MinecraftPlayerAlias;
use App\Entities\Players\Repositories\GameUser;
use App\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use function collect;

/**
 * @deprecated Use MinecraftPlayerAlias model facade instead
 */
class MinecraftPlayerAliasRepository extends Repository
{
    protected $model = MinecraftPlayerAlias::class;

    /**
     * Creates a new MinecraftPlayerAlias tied to a
     * MinecraftPlayer id.
     *
     *
     * @return GameUser
     */
    public function store(string $minecraftPlayerId,
                          string $alias,
                          ?Carbon $registeredAt = null): MinecraftPlayerAlias
    {
        return $this->getModel()->create([
            'player_minecraft_id' => $minecraftPlayerId,
            'alias' => $alias,
            'registered_at' => $registeredAt,
        ]);
    }

    public function getByAlias(string $alias): ?MinecraftPlayerAlias
    {
        return $this->getModel()
            ->where('alias', $alias)
            ->first();
    }

    public function getAllByAlias(string $alias): Collection
    {
        $results = $this->getModel()
            ->where('alias', $alias)
            ->get();

        return $results ? $results : collect();
    }
}
