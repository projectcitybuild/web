<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Http\Data\MinecraftPlayerSessionData;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Illuminate\Pagination\LengthAwarePaginator;

class MinecraftPlayerSessionController extends WebController
{
    use AuthorizesPermissions;

    public function index(MinecraftPlayer $player): LengthAwarePaginator
    {
        $this->requires(WebManagePermission::PLAYERS_VIEW);

        return $player->sessions()
            ->orderByDesc('starts_at')
            ->paginate(50)
            ->through(fn (MinecraftPlayerSession $session): array => MinecraftPlayerSessionData::fromModel($session)->toArray());
    }
}
