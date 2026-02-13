<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\PlayerOpElevations\Exceptions\AlreadyElevatedException;
use App\Domains\PlayerOpElevations\Exceptions\NotElevatedException;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

final class MinecraftOpElevationController extends ApiController
{
    public function start(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'reason' => ['required'],
        ]));

        $uuid = new MinecraftUUID($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        $existing = PlayerOpElevation::where('player_id', $player->getKey())
            ->where('ended_at', '>', now())
            ->first();

        if ($existing !== null) {
            $remaining = now()->diffForHumans($existing->ended_at, [
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'short'  => true,
                'parts'  => 3,
            ]);
            throw new AlreadyElevatedException('You are already OP elevated ('.$remaining.' remaining)');
        }

        return PlayerOpElevation::create([
            'player_id' => $player->getKey(),
            'reason' => $validated->get('reason'),
            'started_at' => now(),
            'ended_at' => now()->addHours(3),
        ]);
    }

    public function end(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
        ]));

        $uuid = new MinecraftUUID($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        $existing = PlayerOpElevation::where('player_id', $player->getKey())
            ->where('ended_at', '>', now())
            ->first();

        throw_if($existing === null, NotElevatedException::class);

        $existing->ended_at = now();
        $existing->save();

        return $existing;
    }
}
