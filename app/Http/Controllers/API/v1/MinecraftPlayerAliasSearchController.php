<?php

namespace App\Http\Controllers\API\v1;

use App\Models\MinecraftPlayerAlias;
use Entities\Resources\MinecraftPlayerAliasResource;
use Illuminate\Http\Request;

class MinecraftPlayerAliasSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        $aliases = MinecraftPlayerAlias::search($query)
            ->take(25)
            ->get();

        return MinecraftPlayerAliasResource::collection($aliases);
    }
}
