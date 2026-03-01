<?php

namespace App\Http\Controllers\Api\v3\Players\Homes;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftPlayerHomeNameController extends ApiController
{
    public function index(Request $request, MinecraftUUID $minecraftUUID)
    {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404, 'Player not found');

        return MinecraftHome::where('player_id', $player->id)
            ->get(['id', 'name']);
    }
}
