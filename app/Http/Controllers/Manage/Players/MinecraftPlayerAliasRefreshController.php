<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MinecraftPlayerAliasRefreshController extends WebController
{
    public function __invoke(MinecraftPlayer $player, LookupMinecraftUUID $lookupMinecraftUUID)
    {
        Gate::authorize('update', $player);

        $lookup = $lookupMinecraftUUID->fetch(MinecraftUUID::tryParse($player->uuid));
        if ($lookup === null) {
            throw new \Exception('UUID not found');
        }

        $updatedAlias = $lookup->username;
        if (empty($updatedAlias)) {
            throw new \Exception('Alias is missing');
        }

        $player->update(['alias' => $updatedAlias]);

        return redirect()
            ->back()
            ->with([
                'player' => $player,
                'success' => 'Alias successfully updated',
            ]);
    }
}
