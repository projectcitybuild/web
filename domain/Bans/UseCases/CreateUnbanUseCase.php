<?php

namespace Domain\Bans\UseCases;

use Domain\Bans\Exceptions\PlayerNotBannedException;
use Entities\Models\Eloquent\GameUnban;
use Illuminate\Support\Facades\DB;
use Repositories\GameBanRepository;
use Repositories\GameUnbanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

class CreateUnbanUseCase
{
    public function __construct(
        private GameBanRepository $gameBanRepository,
        private GameUnbanRepository $gameUnbanRepository,
        private PlayerLookup $playerLookup,
    ) {}

    /**
     * @param PlayerIdentifier $bannedPlayerIdentifier Player currently banned
     * @param PlayerIdentifier $unbannerPlayerIdentifier Player unbanning the banned player
     * @return GameUnban
     * @throws PlayerNotBannedException if the banned player is not actually banned
     */
    public function execute(
        PlayerIdentifier $bannedPlayerIdentifier,
        PlayerIdentifier $unbannerPlayerIdentifier,
    ): GameUnban {
        $existingBan = $this->gameBanRepository->firstActiveBan(identifier: $bannedPlayerIdentifier)
            ?? throw new PlayerNotBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        DB::beginTransaction();
        try {
            $existingBan->is_active = false;
            $existingBan->save();

            $unban = $this->gameUnbanRepository->create(
                banId: $existingBan->getKey(),
                staffPlayerId: $unbannerPlayer->getKey(),
                staffPlayerType: $unbannerPlayerIdentifier->gameIdentifierType->playerType(),
            );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $unban;
    }
}
