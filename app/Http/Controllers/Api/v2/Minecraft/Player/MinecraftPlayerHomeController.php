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
use Illuminate\Validation\ValidationException;

final class MinecraftPlayerHomeController extends ApiController
{
    use ValidatesCoordinates;

    public function index(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            'page_size' => ['integer', 'gt:0'],
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404);

        $defaultSize = 25;
        $pageSize = min($defaultSize, $validated['page_size'] ?? $defaultSize);

        return MinecraftHome::orderBy('name', 'desc')
            ->where('player_id', $player->getKey())
            ->paginate($pageSize);
    }

    public function store(Request $request, MinecraftUUID $minecraftUUID)
    {
        // TODO: check number of homes

        $validated = $request->validate([
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404, 'Player not found');

        $name = $validated['name'];
        $exists = MinecraftHome::where('name', $name)
            ->where('player_id', $player->getKey())
            ->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'name' => ['You already have a home with this name'],
            ]);
        }

        $validated['player_id'] = $player->getKey();

        return MinecraftHome::create($validated);
    }

    public function update(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404, 'Player not found');

        $name = $validated['name'];
        $exists = MinecraftHome::where('name', $name)
            ->where('player_id', $player->getKey())
            ->where('id', '!=', $home->getKey())
            ->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'name' => ['You already have a home with this name'],
            ]);
        }

        $this->assertHasWriteAccess(home: $home, uuid: $minecraftUUID);

        $home->update($validated);

        return $home;
    }

    public function destroy(
        Request $request,
        MinecraftUUID $minecraftUUID,
        MinecraftHome $home,
    ) {
        $this->assertHasWriteAccess(home: $home, uuid: $minecraftUUID);

        $home->delete();

        return response()->json();
    }

    /**
     * Ensures the given player UUID has the ability to modify the given home.
     * Basically checks the UUID is the home owner or a staff member.
     */
    // TODO: use MinecraftHomePolicy
    private function assertHasWriteAccess(MinecraftHome $home, MinecraftUUID $uuid): void
    {
        // TODO: reuse player instance
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        abort_if($player === null, 400, "Player not found");

        $isOwner = $home->player_id === $player->getKey();
        $isStaff = $player->account?->isStaff() ?? false;
        abort_if(!$isOwner && !$isStaff, 403, "Only the owner can edit this");
    }
}
