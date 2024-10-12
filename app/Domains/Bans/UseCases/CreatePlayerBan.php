<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Domains\Bans\Exceptions\AlreadyPermBannedException;
use App\Domains\Bans\Exceptions\AlreadyTempBannedException;
use App\Models\GamePlayerBan;
use Illuminate\Support\Carbon;

final class CreatePlayerBan
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    /**
     * @param  int  $serverId ID of the server the player was banned on
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player to be banned
     * @param  string  $bannedPlayerAlias Name of the player at the time of ban
     * @param  PlayerIdentifier  $bannerPlayerIdentifier Player that created the ban
     * @param  string  $bannerPlayerAlias Name of the player that created the ban at the time
     * @param  string|null  $banReason Reason the player was banned
     * @param  Carbon|null  $expiresAt Date the ban will expire. If null, ban is permanent
     * @return GamePlayerBan
     *
     * @throws AlreadyTempBannedException if a player is already banned temporarily
     * @throws AlreadyPermBannedException if player is already banned permanently
     */
    public function execute(
        int $serverId,
        PlayerIdentifier $bannedPlayerIdentifier,
        string $bannedPlayerAlias,
        PlayerIdentifier $bannerPlayerIdentifier,
        string $bannerPlayerAlias,
        ?string $banReason,
        ?Carbon $expiresAt,
    ): GamePlayerBan {
        $bannedPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannedPlayerIdentifier,
            playerAlias: $bannedPlayerAlias,
        );

        $existingBan = GamePlayerBan::where('banned_player_id', $bannedPlayer->getKey())
            ->active()
            ->first();

        if ($existingBan !== null) {
            if ($existingBan->isTemporaryBan()) {
                throw new AlreadyTempBannedException();
            }
            throw new AlreadyPermBannedException();
        }

        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        return GamePlayerBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'banner_player_id' => $bannerPlayer->getKey(),
            'reason' => $banReason,
            'expires_at' => $expiresAt,
        ]);
    }
}
