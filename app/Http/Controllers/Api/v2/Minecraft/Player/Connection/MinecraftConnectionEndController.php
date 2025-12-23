<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Connection;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftConnectionEndController extends ApiController
{
    public function __construct(
        private readonly UpdateSeenMinecraftPlayer $updateSeenMinecraftPlayer,
    ) {}

    public function __invoke(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
        ]));

        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));

        $this->updateSeenMinecraftPlayer->execute(
            uuid: $uuid,
            alias: $validated->get('alias'),
        );
        return response()->json();
    }
}
