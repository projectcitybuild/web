<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Models\GamePlayerBan;

class CreatePlayerUnban
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    /**
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player currently banned
     * @param  PlayerIdentifier  $unbannerPlayerIdentifier Player unbanning the banned player
     * @return GamePlayerBan
     *
     * @throws NotBannedException if the banned player is not actually banned
     */
    public function execute(
        PlayerIdentifier $bannedPlayerIdentifier,
        PlayerIdentifier $unbannerPlayerIdentifier,
        UnbanType $unbanType,
    ): GamePlayerBan {
        $player = $this->playerLookup->find(identifier: $bannedPlayerIdentifier)
            ?? throw new NotBannedException();

        $existingBan = GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first()
            ?? throw new NotBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
