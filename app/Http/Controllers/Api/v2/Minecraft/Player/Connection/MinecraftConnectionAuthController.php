<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Connection;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Services\IPBanService;
use App\Domains\Bans\Services\PlayerBanService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftConnectionAuthController extends ApiController
{
    public function __construct(
        private readonly PlayerBanService $playerBanService,
        private readonly IPBanService $ipBanService,
    ) {}

    public function __invoke(Request $request, MinecraftUUID $uuid)
    {
        $validated = $request->validate([
            'ip' => ['required', 'ip'],
        ]);

        return response()->json([
            'uuid_ban' => $this->playerBanService->firstActive(playerUuid: $uuid),
            'ip_ban' => $this->ipBanService->find(ip: $validated['ip']),
        ]);
    }
}
