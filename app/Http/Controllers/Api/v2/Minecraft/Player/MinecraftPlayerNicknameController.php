<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftPlayerNicknameController extends ApiController
{
    public function update(Request $request, MinecraftUUID $uuid)
    {
        $validated = $request->validate([
            'nickname' => 'nullable',
        ]);
        MinecraftPlayer::whereUuid($uuid)->update([
            'nickname' => $validated['nickname'],
        ]);
        return MinecraftPlayer::whereUuid($uuid)->first();
    }
}
