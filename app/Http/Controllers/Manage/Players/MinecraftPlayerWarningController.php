<?php

namespace App\Http\Controllers\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;

class MinecraftPlayerWarningController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::WARNINGS_VIEW);

        return $player->warnings()->paginate(50);
    }
}
