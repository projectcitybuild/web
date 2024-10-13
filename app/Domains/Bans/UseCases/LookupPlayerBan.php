<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

class LookupPlayerBan
{
    public function __construct(
        private readonly MojangPlayerApi $mojangPlayerApi,
    ) {}

    public function execute(string $username): ?GamePlayerBan
    {
        $mojangPlayer = $this->mojangPlayerApi->getUuidOf($username);

        if ($mojangPlayer === null) {
            return null;
        }

        $mcPlayer = MinecraftPlayer::where('uuid', $mojangPlayer->getUuid())->first();
        if ($mcPlayer === null) {
            return null;
        }

        $gamePlayerBan = GamePlayerBan::where('banned_player_id', $mcPlayer->getKey())
            ->active()
            ->first();

        if ($gamePlayerBan === null) {
            return null;
        }

        return $gamePlayerBan;
    }
}
