<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Http\WebController;
use Illuminate\Http\Request;
use Library\Mojang\Api\MojangPlayerApi;

class MinecraftPlayerReloadAliasController extends WebController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \App\Exceptions\Http\TooManyRequestsException
     */
    public function __invoke(Request $request, MinecraftPlayer $minecraftPlayer, MojangPlayerApi $api)
    {
        $nameHistory = $api->getNameHistoryOf($minecraftPlayer->uuid)->getNameChanges();
        $currentAlias = array_pop($nameHistory)->getAlias();
        $minecraftPlayer->aliases()->firstOrCreate([
            'alias' => $currentAlias,
        ]);

        return redirect(route('front.panel.minecraft-players.show', $minecraftPlayer));
    }
}
