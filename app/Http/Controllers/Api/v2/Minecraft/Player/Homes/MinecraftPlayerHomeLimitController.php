<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Homes;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Http\Controllers\ApiController;
use App\Models\Group;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftPlayerHomeLimitController extends ApiController
{
    public function __invoke(Request $request, MinecraftUUID $minecraftUUID)
    {
        $player = MinecraftPlayer::whereUuid($minecraftUUID)->first();
        abort_if($player === null, 404, 'Player not found');

        // TODO: reuse
        $account = $player->account;
        $groups = $account?->groups ?: collect();
        if ($account !== null && $account->groups->isEmpty()) {
            $groups->add(Group::whereDefault()->first());
        }
        $max = max(1, $groups->map(fn ($group) => $group->additional_homes ?? 0)->sum());

        $current = MinecraftHome::where('player_id', $player->getKey())->count();

        return compact('current', 'max');
    }
}
