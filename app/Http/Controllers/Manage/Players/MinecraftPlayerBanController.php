<?php

namespace App\Http\Controllers\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;

class MinecraftPlayerBanController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::UUID_BANS_VIEW);

        return $player->gamePlayerBans()->paginate(50);
    }
}
