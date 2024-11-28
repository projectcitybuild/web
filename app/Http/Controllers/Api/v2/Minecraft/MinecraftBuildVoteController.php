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
use Illuminate\Validation\ValidationException;

final class MinecraftBuildVoteController extends ApiController
{
    public function store(Request $request, MinecraftBuild $build)
    {
        $input = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['string'],
        ]);

        $player = MinecraftPlayer::firstOrCreate(
            uuid: MinecraftUUID::tryParse($input['player_uuid']),
            alias: $request->get('alias'),
        );

        if ($build->player_id === $player->getKey()) {
            throw ValidationException::withMessages([
                'error' => 'You cannot vote for your own build',
            ]);
        }

        $alreadyVoted = MinecraftBuildVote::where('player_id', $player->getKey())
            ->where('build_id', $build->getKey())
            ->exists();

        if ($alreadyVoted) {
            throw ValidationException::withMessages([
                'error' => 'You have already voted for this build',
            ]);
        }

        DB::transaction(function () use ($player, $build) {
            $build->votes = $build->votes + 1;
            $build->save();

            MinecraftBuildVote::create([
                'player_id' => $player->getKey(),
                'build_id' => $build->getKey(),
            ]);
        });

        return response()->json($build);
    }

    public function destroy(Request $request, MinecraftBuild $build)
    {
        $input = $request->validate([
            'player_uuid' => ['required', new MinecraftUUIDRule],
        ]);

        $player = MinecraftPlayer::whereUuid(MinecraftUUID::tryParse($input['player_uuid']))
            ->first();

        $vote = MinecraftBuildVote::where('player_id', $player->getKey())
            ->where('build_id', $build->getKey())
            ->first();

        if ($vote === null) {
            throw ValidationException::withMessages([
                'error' => 'You have not voted for this build',
            ]);
        }

        DB::transaction(function () use ($vote, $build) {
            $build->votes = $build->votes - 1;
            $build->save();

            $vote->delete();
        });

        return response()->json($build);
    }
}
