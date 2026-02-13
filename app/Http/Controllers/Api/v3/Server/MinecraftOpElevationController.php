<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\PlayerOpElevations\Services\OpElevationService;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use Illuminate\Http\Request;

final class MinecraftOpElevationController extends ApiController
{
    public function __construct(
        private readonly OpElevationService $opElevationService,
    ) {}

    public function start(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'reason' => ['required'],
        ]));

        $uuid = new MinecraftUUID($validated->get('uuid'));

        return $this->opElevationService->elevate(
            playerUuid: $uuid,
            reason: $validated->get('reason'),
        );
    }

    public function end(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
        ]));

        $uuid = new MinecraftUUID($validated->get('uuid'));

        return $this->opElevationService->end($uuid);
    }
}
