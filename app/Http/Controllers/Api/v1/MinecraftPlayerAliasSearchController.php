<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

class MinecraftPlayerAliasSearchController
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        return MinecraftPlayer::where('alias', 'like', '%'.$query.'%')
            ->take(25)
            ->get();
    }
}
