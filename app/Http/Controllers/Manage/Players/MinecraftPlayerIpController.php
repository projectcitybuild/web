<?php

namespace App\Http\Controllers\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;

class MinecraftPlayerIpController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        return $player->ips()->orderBy('updated_at', 'desc')->paginate(50);
    }
}
