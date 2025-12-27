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
        ]));

        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();

        $updated = [];
        if ($validated->has('alias')) {
            $updated['alias'] = $validated->get('alias');
        }
        if ($validated->has('nickname')) {
            $updated['nickname'] = $validated->get('nickname');
        }

        if (! empty($updated)) {
            $player->update($updated);
        }
        return $player->refresh();
    }
}
