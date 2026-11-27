<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerWorldTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MinecraftPlayerWorldTimeController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'increments' => ['required', 'array', 'min:1', 'max:100'],
            'increments.*' => ['required', 'array:player_uuid,world_uuid,game_mode,duration_seconds'],
            'increments.*.player_uuid' => ['required', new MinecraftUUIDRule],
            'increments.*.world_uuid' => ['required', 'uuid'],
            'increments.*.game_mode' => ['required', 'in:survival,creative,adventure,spectator'],
            'increments.*.duration_seconds' => ['required', 'integer', 'min:1', 'max:2592000'],
        ]);

        $playerUuids = collect($validated['increments'])
            ->map(fn (array $increment) => (new MinecraftUUID($increment['player_uuid']))->trimmed())
            ->unique()
            ->values();

        $players = MinecraftPlayer::query()
            ->whereIn('uuid', $playerUuids)
            ->get()
            ->keyBy('uuid');

        if ($playerUuids->diff($players->keys())->isNotEmpty()) {
            throw ValidationException::withMessages([
                'increments' => ['One or more player UUIDs do not exist.'],
            ]);
        }

        DB::transaction(function () use ($validated, $players) {
            $playerIds = collect($validated['increments'])
                ->map(fn (array $increment) => $players->get(
                    (new MinecraftUUID($increment['player_uuid']))->trimmed()
                )->id)
                ->unique()
                ->sort()
                ->values();

            MinecraftPlayer::query()
                ->whereIn('id', $playerIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            foreach ($validated['increments'] as $increment) {
                $playerUuid = (new MinecraftUUID($increment['player_uuid']))->trimmed();
                $player = $players->get($playerUuid);
                $worldUuid = strtolower($increment['world_uuid']);
                $worldTime = MinecraftPlayerWorldTime::query()->firstOrCreate([
                    'player_id' => $player->id,
                    'world_uuid' => $worldUuid,
                ]);
                $counter = "{$increment['game_mode']}_time";
                $worldTime->increment($counter, (int) $increment['duration_seconds']);
            }
        });

        return response()->noContent();
    }
}
