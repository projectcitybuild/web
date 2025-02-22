<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class MinecraftPlayerHomeController extends ApiController
{
    use ValidatesCoordinates;

    public function index(Request $request)
    {
        $request->validate([
           'page_size' => ['integer', 'gt:0'],
        ]);

        $defaultSize = 25;
        $pageSize = min($defaultSize, $request->get('page_size', $defaultSize));

        return MinecraftHome::orderBy('votes', 'desc')
            ->paginate($pageSize);
    }

    public function store(Request $request)
    {
        // TODO: don't allow duplicate names for a player
        // TODO: check number of homes

        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::firstOrCreate(
            uuid: new MinecraftUUID($validated['player_uuid']),
            alias: $request->get('alias'),
        );
        $validated['player_id'] = $player->getKey();

        return MinecraftHome::create($validated);
    }

    public function update(Request $request, MinecraftHome $home)
    {
        // TODO: how to determine $home?
        // TODO: don't allow duplicate names for a player

        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $this->assertHasWriteAccess(home: $home, uuid: $validated['player_uuid']);

        $home->update($validated);

        return $home;
    }

    public function destroy(Request $request, MinecraftHome $home)
    {
        // TODO: how to determine $home?

        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
        ]);

        $this->assertHasWriteAccess(home: $home, uuid: $validated['player_uuid']);

        $home->delete();

        return response()->json();
    }

    /**
     * Ensures the given player UUID has the ability to modify the given build.
     * Basically checks the UUID is the build owner or a staff member.
     */
    // TODO: use MinecraftHomePolicy
    private function assertHasWriteAccess(MinecraftHome $home, string $uuid): void
    {
        $uuid = new MinecraftUUID($uuid);
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        abort_if($player === null, 400, "Player not found");

        $isOwner = $home->player_id === $player->getKey();
        $isStaff = $player->account?->isStaff() ?? false;
        abort_if(!$isOwner && !$isStaff, 403, "Only the owner can edit this");
    }
}
