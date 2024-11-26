<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftBuild;
use App\Models\MinecraftBuildVote;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class MinecraftBuildController extends ApiController
{
    public function index(Request $request)
    {
        $request->validate([
           'page_size' => ['integer', 'gt:0'],
        ]);

        $defaultSize = 25;
        $pageSize = min($defaultSize, $request->get('page_size', $defaultSize));

        return MinecraftBuild::orderBy('votes', 'desc')
            ->paginate($pageSize);
    }

    public function show(Request $request, MinecraftBuild $build)
    {
        return $build;
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => ['required', Rule::unique(MinecraftBuild::tableName())],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        $player = MinecraftPlayer::firstOrCreate(
            uuid: new MinecraftUUID($input['player_uuid']),
            alias: $request->get('alias'),
        );
        $input['player_id'] = $player->getKey();

        return MinecraftBuild::create($input);
    }

    public function update(Request $request, MinecraftBuild $build)
    {
        $input = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'name' => ['required', Rule::unique(MinecraftBuild::tableName())->ignore($build)],
            'world' => 'required|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'z' => 'required|numeric',
            'pitch' => 'required|numeric',
            'yaw' => 'required|numeric',
        ]);

        $uuid = new MinecraftUUID($input['player_uuid']);
        $player = MinecraftPlayer::whereUuid($uuid)->with('account.groups')->first();
        if ($player === null) {
            abort(403);
        }

        $isBuildOwner = $build->player_id === $player->getKey();

        // TODO: also check their group permission
        if (!$isBuildOwner) {
            abort(403);
        }

        $build->update($request->all());

        return $build;
    }

    public function destroy(Request $request, MinecraftBuild $build)
    {
        $input = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
        ]);

        $uuid = new MinecraftUUID($input['player_uuid']);
        $player = MinecraftPlayer::whereUuid($uuid)->with('account.groups')->first();
        if ($player === null) {
            abort(403);
        }

        $isBuildOwner = $build->player_id === $player->getKey();

        // TODO: also check their group permission
        if (!$isBuildOwner) {
            abort(403);
        }

        DB::transaction(function () use ($build) {
            MinecraftBuildVote::where('build_id', $build->getKey())->delete();
            $build->delete();
        });

        return response()->json();
    }
}
