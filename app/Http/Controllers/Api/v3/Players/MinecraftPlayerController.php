<?php

namespace App\Http\Controllers\Api\v3\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

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

        if (! empty($updated)) {
            $player->update($updated);
        }
        return $player->refresh();
    }
}
