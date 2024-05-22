<?php

namespace App\Http\Controllers\Minecraft\Players;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftPlayerSyncController extends Controller
{
    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $uuid = str_replace(search: '-',  replace: '', subject: $uuid);
        $player = Player::where('uuid', $uuid)->firstOrFail();

        $account = $player->linkedAccount;

        // TODO: auto correct broken/missing ranks

        // TODO: return account and sync data

        return response()->json([]);
    }
}
