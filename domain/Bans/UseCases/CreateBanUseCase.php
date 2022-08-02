<?php

namespace Domain\Bans\UseCases;

use Domain\Bans\Exceptions\PlayerAlreadyBannedException;
use Entities\Models\Eloquent\GameBan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class CreateBanUseCase
{
    public function __construct(
        private GameBanRepository $gameBanRepository,
        private PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  int  $serverId ID of the server the player was banned on
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player to be banned
     * @param  string  $bannedPlayerAlias Name of the player at the time of ban
     * @param  PlayerIdentifier  $bannerPlayerIdentifier Player that created the ban
     * @param  string  $bannerPlayerAlias Name of the player that created the ban at the time
     * @param  string|null  $banReason Reason the player was banned
     * @param  Carbon|null  $expiresAt Date the ban will expire. If null, ban is permanent
     * @return GameBan
     *
     * @throws PlayerAlreadyBannedException if player is already banned globally or on the same server
     */
    public function execute(
        int $serverId,
        PlayerIdentifier $bannedPlayerIdentifier,
        string $bannedPlayerAlias,
        PlayerIdentifier $bannerPlayerIdentifier,
        string $bannerPlayerAlias,
        ?string $banReason,
        ?Carbon $expiresAt,
    ): GameBan {
        $bannedPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannedPlayerIdentifier,
            playerAlias: $bannedPlayerAlias,
        );

        $isPermanentBan = $expiresAt === null;
        $existingBan = $this->gameBanRepository->firstActiveBan(
            player: $bannedPlayer,
            skipTempBans: $isPermanentBan, // Permanent bans can override temporary bans
        );
        if ($existingBan !== null) {
            throw new PlayerAlreadyBannedException();
        }

        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        DB::beginTransaction();
        try {
            if ($isPermanentBan) {
                $this->gameBanRepository->deactivateAllTemporaryBans(player: $bannedPlayer);
            }
            $ban = $this->gameBanRepository->create(
                serverId: $serverId,
                bannedPlayerId: $bannedPlayer->getKey(),
                bannedPlayerAlias: $bannedPlayerAlias,
                bannerPlayerId: $bannerPlayer->getKey(),
                reason: $banReason,
                expiresAt: $expiresAt,
            );
            DB::commit();

            return $ban;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
