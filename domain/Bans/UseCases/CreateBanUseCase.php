<?php

namespace Domain\Bans\UseCases;

use Domain\Bans\Exceptions\PlayerAlreadyBannedException;
use Entities\Models\Eloquent\GameBan;
use Illuminate\Support\Carbon;
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
     * @param  bool  $isGlobalBan Whether the player will be banned from all servers, or just the server they were banned on
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
        bool $isGlobalBan,
        ?string $banReason,
        ?Carbon $expiresAt,
    ): GameBan {
        $existingBan = $this->gameBanRepository->firstActiveBan(identifier: $bannedPlayerIdentifier);
        if ($existingBan !== null) {
            throw new PlayerAlreadyBannedException();
        }

        $bannedPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannedPlayerIdentifier,
            playerAlias: $bannedPlayerAlias,
        );
        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        return $this->gameBanRepository->create(
            serverId: $serverId,
            bannedPlayerId: $bannedPlayer->getKey(),
            bannedPlayerType: $bannedPlayerIdentifier->gameIdentifierType->playerType(),
            bannedPlayerAlias: $bannedPlayerAlias,
            bannerPlayerId: $bannerPlayer->getKey(),
            bannerPlayerType: $bannerPlayerIdentifier->gameIdentifierType->playerType(),
            isGlobalBan: $isGlobalBan,
            reason: $banReason,
            expiresAt: $expiresAt,
        );
    }
}
