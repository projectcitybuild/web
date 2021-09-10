<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\WebController;
use App\Library\Mojang\Api\MojangPlayerApi;
use Illuminate\Http\Request;

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
