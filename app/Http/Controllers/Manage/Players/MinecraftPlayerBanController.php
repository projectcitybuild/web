<?php

namespace App\Http\Controllers\Manage\Players;

use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Gate;

class MinecraftPlayerBanController extends WebController
{
    public function index(MinecraftPlayer $player)
    {
        Gate::authorize('viewAny', GamePlayerBan::class);

        return $player->gamePlayerBans()->cursorPaginate(50);
    }
}
