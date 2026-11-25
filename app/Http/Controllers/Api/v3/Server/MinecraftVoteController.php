<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\RequireServerToken;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class MinecraftVoteController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'uuid'],
            'player_uuid' => ['required', new MinecraftUUIDRule],
            'username' => ['sometimes', 'nullable', 'string', 'max:191'],
            'service_name' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string', 'max:191'],
            'timestamp' => ['required', 'integer', 'min:0', 'max:253402300799999'],
            'additional_data' => ['sometimes', 'nullable', 'string', 'max:65535'],
        ]);

        $existingVote = MinecraftVote::where('event_id', $validated['event_id'])->first();

        if ($existingVote !== null) {
            return response()->json(['id' => $existingVote->id]);
        }

        $player = MinecraftPlayer::firstOrCreate(
            uuid: new MinecraftUUID($validated['player_uuid']),
            alias: $validated['username'] ?? null,
        );

        $vote = MinecraftVote::firstOrCreate(
            ['event_id' => $validated['event_id']],
            [
                'server_id' => $request->attributes->get(RequireServerToken::SERVER_ID_ATTRIBUTE),
                'player_id' => $player->id,
                'service_name' => $validated['service_name'],
                'address' => $validated['address'],
                'source_timestamp' => $validated['timestamp'],
                'voted_at' => Carbon::createFromTimestampMs((int) $validated['timestamp'], 'UTC'),
                'additional_data' => $validated['additional_data'] ?? null,
            ],
        );

        return response()->json(['id' => $vote->id], $vote->wasRecentlyCreated ? 201 : 200);
    }
}
