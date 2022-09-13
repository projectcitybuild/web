<?php

namespace App\Http\Controllers\Api\v1;

use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Resources\MinecraftPlayerResource;
use Illuminate\Http\Request;

class MinecraftPlayerAliasSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        $accounts = MinecraftPlayerAlias::search($query)
            ->limit(25)
            ->with('minecraftPlayer')
            ->get();

        return MinecraftPlayerResource::collection($accounts);
    }
}
