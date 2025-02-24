<?php

namespace App\Http\Controllers\Manage\Players;

use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Support\Facades\Gate;

class MinecraftPlayerIpController extends WebController
{
    public function index(MinecraftPlayer $player)
    {
        Gate::authorize('viewAny', MinecraftPlayer::class);

        return $player->ips()->orderBy('updated_at', 'desc')->paginate(50);
    }
}
