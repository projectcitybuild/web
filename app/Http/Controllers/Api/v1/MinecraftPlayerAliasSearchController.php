<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\MinecraftPlayerAliasResource;
use App\Models\MinecraftPlayerAlias;
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
