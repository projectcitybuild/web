<?php

namespace App\Http\Controllers\Manage\Players;

use App\Http\Controllers\WebController;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Gate;

class MinecraftPlayerHomeController extends WebController
{
    public function index(MinecraftPlayer $player)
    {
        Gate::authorize('viewAny', MinecraftHome::class);

        return $player->homes()->paginate(50);
    }
}
