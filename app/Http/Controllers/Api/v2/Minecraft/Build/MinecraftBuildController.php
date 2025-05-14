<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Build;

use App\Core\Domains\MinecraftCoordinate\ValidatesCoordinates;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\Pagination\HasPaginatedApi;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftBuild;
use App\Models\MinecraftBuildVote;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class MinecraftBuildController extends ApiController
{
    use ValidatesCoordinates;
    use HasPaginatedApi;

    public function index(Request $request)
    {
        $validated = $request->validate([
            ...$this->paginationRules,
        ]);

        $pageSize = $this->pageSize($validated);

        return MinecraftBuild::orderBy('votes', 'desc')
            ->paginate($pageSize);
    }

    public function show(Request $request, MinecraftBuild $build)
    {
        $build->load('player');
        return $build;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'alias' => 'required',
            'name' => ['required', Rule::unique(MinecraftBuild::tableName())],
            ...$this->coordinateRules,
        ]);

        $player = MinecraftPlayer::firstOrCreate(
            uuid: new MinecraftUUID($validated['player_uuid']),
            alias: $validated['alias'],
        );
        $validated['player_id'] = $player->getKey();

        return MinecraftBuild::create($validated);
    }

    public function update(Request $request, MinecraftBuild $build)
    {
        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => ['required', Rule::unique(MinecraftBuild::tableName())->ignore($build)],
            ...$this->coordinateRules,
        ]);

        $this->assertHasWriteAccess(build: $build, uuid: $validated['player_uuid']);

        $build->update($validated);

        return $build;
    }

    public function patch(Request $request, MinecraftBuild $build)
    {
        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => [Rule::unique(MinecraftBuild::tableName())->ignore($build)],
        ]);

        $this->assertHasWriteAccess(build: $build, uuid: $validated['player_uuid']);

        if ($request->has('name')) {
            $name = $request->get('name');
            $build->name = empty($name) ? null : $name;
        }
        if ($request->has('description')) {
            $description = $request->get('description');
            $build->description = empty($description) ? null : $description;
        }
        if ($request->has('lore')) {
            $lore = $request->get('lore');
            $build->lore = empty($lore) ? null : $lore;
        }
        $build->save();

        return $build;
    }

    public function destroy(Request $request, MinecraftBuild $build)
    {
        $validated = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
        ]);

        $this->assertHasWriteAccess(build: $build, uuid: $validated['player_uuid']);

        DB::transaction(function () use ($build) {
            MinecraftBuildVote::where('build_id', $build->getKey())->delete();
            $build->delete();
        });

        return response()->json();
    }

    /**
     * Ensures the given player UUID has the ability to modify the given build.
     * Basically checks the UUID is the build owner or a staff member.
     */
    // TODO: use MinecraftBuildPolicy
    private function assertHasWriteAccess(MinecraftBuild $build, string $uuid): void
    {
        $uuid = new MinecraftUUID($uuid);
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        abort_if($player === null, 400, "Player not found");

        $isBuildOwner = $build->player_id === $player->getKey();
        $isStaff = $player->account?->isStaff() ?? false;
        abort_if(!$isBuildOwner && !$isStaff, 403, "Only the build owner can edit this build");
    }
}
