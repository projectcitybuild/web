<?php

namespace App\Http\Controllers\Manage\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\MinecraftPlayer;
use Illuminate\Validation\ValidationException;

class MinecraftPlayerAliasRefreshController extends WebController
{
    use AuthorizesPermissions;

    public function __invoke(MinecraftPlayer $player, LookupMinecraftUUID $lookupMinecraftUUID)
    {
        $this->requires(WebManagePermission::PLAYERS_EDIT);

        $lookup = $lookupMinecraftUUID->fetch(MinecraftUUID::tryParse($player->uuid));
        if ($lookup === null) {
            throw ValidationException::withMessages(['UUID not found']);
        }

        $updatedAlias = $lookup->username;
        if (empty($updatedAlias)) {
            throw new \Exception('Alias is missing');
        }

        $player->update(['alias' => $updatedAlias]);

        return $player;
    }
}
