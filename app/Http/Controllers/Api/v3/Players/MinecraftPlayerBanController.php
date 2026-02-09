<?php

namespace App\Http\Controllers\Api\v3\Players;

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
        $validated = collect($request->validate([
            'only_active' => ['nullable', 'boolean'],
        ]));

        return $this->playerBanService->allForUuid(
            playerUuid: $uuid,
            onlyActive: $validated->get('only_active', false),
        );
    }
}
