<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Services\PlayerBanService;
use Illuminate\Http\Request;

class MinecraftPlayerBanController
{
    public function __construct(
        private readonly PlayerBanService $playerBanService,
    ) {}

    public function index(Request $request, MinecraftUUID $uuid)
    {
        return $this->playerBanService->allForUuid(playerUuid: $uuid);
    }
}
