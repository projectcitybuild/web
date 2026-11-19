<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Http\Data\MinecraftPlayerIpData;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerIp;

class MinecraftPlayerIpController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        return $player
            ->ips()
            ->orderBy('times_connected', 'desc')
            ->paginate(50)
            ->through(fn (MinecraftPlayerIp $ip): array => MinecraftPlayerIpData::fromModel($ip)->toArray());
    }
}
