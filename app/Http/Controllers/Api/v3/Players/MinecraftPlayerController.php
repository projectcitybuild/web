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
        if ($validated->has('alias')) {
            $updated['alias'] = $validated->get('alias');
        }
        if ($validated->has('nickname')) {
            $updated['nickname'] = $validated->get('nickname');
        }
        if ($validated->has('muted')) {
            $updated['muted'] = $validated->get('muted');
        }
        if ($validated->has('walk_speed')) {
            $updated['walk_speed'] = $validated->get('walk_speed');
        }
        if ($validated->has('fly_speed')) {
            $updated['fly_speed'] = $validated->get('fly_speed');
        }
        if ($validated->has('teleport_mode')) {
            $updated['teleport_mode'] = $validated->get('teleport_mode');
        }
        if ($validated->has('move_mode')) {
            $updated['move_mode'] = $validated->get('move_mode');
        }
        foreach ([
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
