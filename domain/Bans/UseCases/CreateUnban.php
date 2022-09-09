<?php

namespace Domain\Bans\UseCases;

use Domain\Bans\Exceptions\NotBannedException;
use Entities\Models\Eloquent\GameUnban;
use Illuminate\Support\Facades\DB;
use Repositories\GameBanRepository;
use Repositories\GameUnbanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

class CreateUnban
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly GameUnbanRepository $gameUnbanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player currently banned
     * @param  PlayerIdentifier  $unbannerPlayerIdentifier Player unbanning the banned player
     * @return GameUnban
     *
     * @throws NotBannedException if the banned player is not actually banned
     */
    public function execute(
        PlayerIdentifier $bannedPlayerIdentifier,
        PlayerIdentifier $unbannerPlayerIdentifier,
    ): GameUnban {
        $player = $this->playerLookup->find(identifier: $bannedPlayerIdentifier)
            ?? throw new NotBannedException();

        $existingBan = $this->gameBanRepository->firstActiveBan(player: $player)
            ?? throw new NotBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        DB::beginTransaction();
        try {
            $existingBan->is_active = false;
            $existingBan->save();

            $unban = $this->gameUnbanRepository->create(
                banId: $existingBan->getKey(),
                staffPlayerId: $unbannerPlayer->getKey(),
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $unban;
    }
}