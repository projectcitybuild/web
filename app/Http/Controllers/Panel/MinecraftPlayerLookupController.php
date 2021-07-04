<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use App\Exceptions\Http\TooManyRequestsException;
use App\Http\WebController;
use App\Library\Mojang\Api\MojangPlayerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MinecraftPlayerLookupController extends WebController
{
    /**
     * Lookup a minecraft player by their dashed or undashed UUID
     *
     * @param $uuid
     * @return MinecraftPlayer
     */
    private function lookupByUUID($uuid): ?MinecraftPlayer
    {
        if (strlen($uuid) != 32 && strlen($uuid) != 36) return null;

        $uuid = str_replace('-', '', $uuid);
        return MinecraftPlayer::where('uuid', $uuid)->first();
    }

    private function lookupByStoredAlias($alias): ?MinecraftPlayer
    {
        if (strlen($alias) < 3 || strlen($alias) > 16) return null;

        $mcPlayerAlias = MinecraftPlayerAlias::where('alias', $alias)->first();

        if ($mcPlayerAlias == null) return null;
        return $mcPlayerAlias->minecraftPlayer;
    }

    private function lookupByLiveAlias($alias): ?MinecraftPlayer
    {
        if (strlen($alias) < 3 || strlen($alias) > 16) return null;

        $api = App::make(MojangPlayerApi::class);

        try {
            $mojangPlayer = $api->getUuidOf($alias);
            return $this->lookupByUUID($mojangPlayer->getUuid());
        } catch (TooManyRequestsException $e) {
            return null;
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        $minecraftPlayer = $this->lookupByUUID($query) ??
            $this->lookupByStoredAlias($query) ??
            $this->lookupByLiveAlias($query);

        if ($minecraftPlayer == null) {
            // TODO add flash error
            return redirect(route('front.panel.minecraft-players.index'));
        }

        return redirect(route('front.panel.minecraft-players.show', $minecraftPlayer));
    }
}
