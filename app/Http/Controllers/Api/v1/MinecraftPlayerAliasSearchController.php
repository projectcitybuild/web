<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\MinecraftPlayerAlias;
use Illuminate\Http\Request;

class MinecraftPlayerAliasSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        return MinecraftPlayerAlias::search($query)
            ->take(25)
            ->get();
    }
}
