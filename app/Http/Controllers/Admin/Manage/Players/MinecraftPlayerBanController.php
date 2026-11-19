<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminGamePlayerBanData;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

class MinecraftPlayerBanController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::UUID_BANS_VIEW);

        return $player->gamePlayerBans()
            ->with(['bannedPlayer', 'bannerPlayer', 'unbannerPlayer'])
            ->paginate(50)
            ->through(fn (GamePlayerBan $ban): array => AdminGamePlayerBanData::fromModel($ban)->toArray());
    }
}
