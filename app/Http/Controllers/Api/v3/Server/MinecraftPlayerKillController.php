<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerKill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MinecraftPlayerKillController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kills' => ['required', 'array', 'min:1', 'max:100'],
            'kills.*' => ['required', 'array:event_id,attacker_uuid,victim_uuid,weapon,killed_at'],
            'kills.*.event_id' => ['required', 'uuid', 'distinct'],
            'kills.*.attacker_uuid' => ['required', new MinecraftUUIDRule],
            'kills.*.victim_uuid' => ['required', new MinecraftUUIDRule],
            'kills.*.weapon' => ['sometimes', 'nullable', 'string', 'max:100'],
            'kills.*.killed_at' => ['required', 'date'],
        ]);

        $playerUuids = collect($validated['kills'])
            ->flatMap(fn (array $kill) => [
                (new MinecraftUUID($kill['attacker_uuid']))->trimmed(),
                (new MinecraftUUID($kill['victim_uuid']))->trimmed(),
            ])
            ->unique()
            ->values();

        $players = MinecraftPlayer::query()
            ->whereIn('uuid', $playerUuids)
            ->get()
            ->keyBy('uuid');

        if ($playerUuids->diff($players->keys())->isNotEmpty()) {
            throw ValidationException::withMessages([
                'kills' => ['One or more player UUIDs do not exist.'],
            ]);
        }

        foreach ($validated['kills'] as $index => $kill) {
            if ((new MinecraftUUID($kill['attacker_uuid']))->trimmed() ===
                (new MinecraftUUID($kill['victim_uuid']))->trimmed()) {
                throw ValidationException::withMessages([
                    "kills.$index.victim_uuid" => ['The victim must be different from the attacker.'],
                ]);
            }
        }

        DB::transaction(function () use ($validated, $players) {
            $eventIds = collect($validated['kills'])->pluck('event_id');
            $existingEventIds = MinecraftPlayerKill::query()
                ->whereIn('event_id', $eventIds)
                ->pluck('event_id')
                ->all();

            foreach ($validated['kills'] as $kill) {
                if (in_array($kill['event_id'], $existingEventIds, true)) {
                    continue;
                }

                $attackerUuid = (new MinecraftUUID($kill['attacker_uuid']))->trimmed();
                $victimUuid = (new MinecraftUUID($kill['victim_uuid']))->trimmed();

                MinecraftPlayerKill::query()->create([
                    'event_id' => $kill['event_id'],
                    'player_id' => $players->get($attackerUuid)->id,
                    'victim_player_id' => $players->get($victimUuid)->id,
                    'weapon' => $kill['weapon'] ?? null,
                    'killed_at' => Carbon::parse($kill['killed_at'])->utc(),
                ]);
            }
        });

        return response()->noContent();
    }
}
