<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\MinecraftPlayerAlias;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use function collect;

class MinecraftPlayerAliasRepository
{
    /**
     * Creates a new MinecraftPlayerAlias tied to a
     * MinecraftPlayer id.
     */
    public function store(
        string $minecraftPlayerId,
        string $alias,
        ?Carbon $registeredAt = null
    ): MinecraftPlayerAlias {
        return MinecraftPlayerAlias::create([
            'player_minecraft_id' => $minecraftPlayerId,
            'alias' => $alias,
            'registered_at' => $registeredAt,
        ]);
    }

    public function getByAlias(string $alias): ?MinecraftPlayerAlias
    {
        return MinecraftPlayerAlias::where('alias', $alias)->first();
    }

    public function getAllByAlias(string $alias): Collection
    {
        return MinecraftPlayerAlias::where('alias', $alias)->get()
            ?? collect();
    }
}
