<?php

namespace App\Http\Controllers\Panel;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MinecraftPlayerLookupController extends WebController
{
    /**
     * Lookup a minecraft player by their dashed or un-dashed UUID.
     *
     * @param $uuid
     */
    private function lookupByUUID($uuid): ?MinecraftPlayer
    {
        if (strlen($uuid) != 32 && strlen($uuid) != 36) {
            return null;
        }

        $uuid = str_replace('-', '', $uuid);

        return MinecraftPlayer::where('uuid', $uuid)->first();
    }

    /**
     * Lookup a minecraft player by their previously fetched alias.
     *
     * @param $alias
     */
    private function lookupByStoredAlias($alias): ?MinecraftPlayer
    {
        if (strlen($alias) < 3 || strlen($alias) > 16) {
            return null;
        }

        $mcPlayerAlias = MinecraftPlayerAlias::where('alias', $alias)->first();

        if ($mcPlayerAlias == null) {
            return null;
        }

        return $mcPlayerAlias->minecraftPlayer;
    }

    /**
     * Lookup a minecraft player by their UUID from the Mojang API.
     *
     * @param $alias
     */
    private function lookupByLiveAlias($alias): ?MinecraftPlayer
    {
        if (strlen($alias) < 3 || strlen($alias) > 16) {
            return null;
        }

        $api = App::make(MojangPlayerApi::class);

        try {
            $mojangPlayer = $api->getUuidOf($alias);

            if ($mojangPlayer == null) {
                return null;
            }

            return $this->lookupByUUID($mojangPlayer->getUuid());
        } catch (TooManyRequestsException $e) {
            return null;
        }
    }

    /**
     * Handle the incoming request.
     *
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
