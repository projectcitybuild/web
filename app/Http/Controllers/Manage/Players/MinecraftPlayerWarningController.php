<?php

namespace App\Http\Controllers\Manage\Players;

use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Support\Facades\Gate;

class MinecraftPlayerWarningController extends WebController
{
    public function index(MinecraftPlayer $player)
    {
        Gate::authorize('viewAny', PlayerWarning::class);

        return $player->warnings()->cursorPaginate(50);
    }
}
