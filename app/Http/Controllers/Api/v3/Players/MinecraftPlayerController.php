<?php

namespace App\Http\Controllers\Api\v3\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Players\Data\MoveMode;
use App\Domains\Players\Data\TeleportMode;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MinecraftPlayerController
{
    public function show(Request $request, MinecraftUUID $uuid)
    {
        return MinecraftPlayer::whereUuid($uuid)->firstOrFail();
    }

    public function update(Request $request, MinecraftUUID $uuid)
    {
        $validated = collect($request->validate([
            'alias' => ['nullable'],
            'nickname' => ['sometimes', 'max:100'],
            'muted' => ['sometimes', 'boolean'],
            'walk_speed' => ['sometimes', 'numeric'],
            'fly_speed' => ['sometimes', 'numeric'],
            'teleport_mode' => ['sometimes', Rule::enum(TeleportMode::class)],
            'move_mode' => ['sometimes', Rule::enum(MoveMode::class)],
            'last_location_world_uuid' => ['sometimes', 'nullable', 'uuid'],
            'last_location_x' => ['sometimes', 'nullable', 'numeric'],
            'last_location_y' => ['sometimes', 'nullable', 'numeric'],
            'last_location_z' => ['sometimes', 'nullable', 'numeric'],
            'last_location_pitch' => ['sometimes', 'nullable', 'numeric'],
            'last_location_yaw' => ['sometimes', 'nullable', 'numeric'],
        ]));

        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();

        $updated = [];
        foreach ([
            'alias',
            'nickname',
            'muted',
            'walk_speed',
            'fly_speed',
            'teleport_mode',
            'move_mode',
            'last_location_world_uuid',
            'last_location_x',
            'last_location_y',
            'last_location_z',
            'last_location_pitch',
            'last_location_yaw',
        ] as $field) {
            if ($validated->has($field)) {
                $updated[$field] = $validated->get($field);
            }
        }

        if (! empty($updated)) {
            $player->update($updated);
        }
        return $player->refresh();
    }
}
