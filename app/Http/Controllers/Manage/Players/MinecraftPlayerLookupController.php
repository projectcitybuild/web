<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class MinecraftPlayerLookupController extends WebController
{
    public function __invoke(Request $request)
    {
        Gate::authorize('viewAny', MinecraftPlayer::class);

        $query = $request->input('query');

        if (empty($query)) {
            return redirect(route('manage.minecraft-players.index'));
        }

        $minecraftPlayer = $this->lookupByUUID($query) ??
            $this->lookupByStoredAlias($query) ??
            $this->lookupByLiveAlias($query);

        if ($minecraftPlayer == null) {
            // TODO add flash error
            return redirect(route('manage.minecraft-players.index'));
        }

        return redirect(route('manage.minecraft-players.show', $minecraftPlayer));
    }

    private function lookupByUUID(string $uuid): ?MinecraftPlayer
    {
        try {
            $uuid = new MinecraftUUID($uuid);
            return MinecraftPlayer::whereUuid($uuid)->first();
        } catch (\Exception $e) {
            return null;
        }
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
        return MinecraftPlayer::where('alias', 'like', '%'.$alias.'%')->first();
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
}
