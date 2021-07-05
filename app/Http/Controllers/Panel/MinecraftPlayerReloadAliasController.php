<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use App\Http\WebController;
use App\Library\Mojang\Api\MojangPlayerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MinecraftPlayerReloadAliasController extends WebController
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param MinecraftPlayer $minecraftPlayer
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, MinecraftPlayer $minecraftPlayer)
    {
        $api = App::make(MojangPlayerApi::class);
        $nameHistory = $api->getNameHistoryOf($minecraftPlayer->uuid)->getNameChanges();
        $currentAlias = array_pop($nameHistory)->getAlias();
        $minecraftPlayer->aliases()->firstOrCreate([
            'alias' => $currentAlias,
        ]);

        return redirect(route('front.panel.minecraft-players.show', $minecraftPlayer));
    }
}
