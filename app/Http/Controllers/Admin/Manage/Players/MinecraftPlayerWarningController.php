<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminPlayerWarningData;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;

class MinecraftPlayerWarningController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player)
    {
        $this->requires(WebManagePermission::WARNINGS_VIEW);

        return $player->warnings()
            ->with(['warnedPlayer', 'warnerPlayer'])
            ->paginate(50)
            ->through(fn (PlayerWarning $warning): array => AdminPlayerWarningData::fromModel($warning)->toArray());
    }
}
