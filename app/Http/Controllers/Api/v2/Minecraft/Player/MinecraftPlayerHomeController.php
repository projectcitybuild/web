<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class MinecraftPlayerHomeController extends ApiController
{
    use ValidatesCoordinates;
    use HasPaginatedApi;

    public function index(Request $request, MinecraftUUID $minecraftUUID)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404);

        $pageSize = $this->pageSize($validated);

        return MinecraftHome::orderBy('name', 'desc')
            ->where('player_id', $player->getKey())
            ->paginate($pageSize);
    }

    public function show(Request $request, MinecraftUUID $minecraftUUID, MinecraftHome $home)
    {
        $this->assertHasWriteAccess(home: $home, uuid: $minecraftUUID);
        return $home;
    }

    public function store(Request $request, MinecraftUUID $minecraftUUID)
    {
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

        $allowedHomes = $player
            ->account
            ?->groups
            ?->map(fn ($group) => $group->additional_homes ?? 0)
            ?->sum() ?? 1;
        $homes = MinecraftHome::where('player_id', $player->getKey())->count();
        if ($homes >= $allowedHomes) {
            throw ValidationException::withMessages([
                'error' => ['You reached your home limit of '.$allowedHomes],
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
