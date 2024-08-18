<?php

namespace Repositories;

use App\Models\MinecraftPlayerAlias;
use Carbon\Carbon;

/**
 * @final
 */
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
}
